<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use Throwable;

/**
 * Modèle des réservations.
 */
final class Reservation
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Retourne les réservations d'un utilisateur.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findByUserId(int $userId): array
    {
        $sql = <<<SQL
            SELECT
                r.id,
                r.trajet_id,
                r.utilisateur_id,
                r.created_at,
                t.auteur_id,
                t.date_depart,
                t.date_arrivee,
                ad.nom AS departure_agency,
                aa.nom AS arrival_agency,
                u.prenom AS author_first_name,
                u.nom AS author_last_name,
                u.email AS author_email,
                u.telephone AS author_phone
            FROM reservations r
            INNER JOIN trajets t ON t.id = r.trajet_id
            INNER JOIN agences ad ON ad.id = t.agence_depart_id
            INNER JOIN agences aa ON aa.id = t.agence_arrivee_id
            INNER JOIN utilisateurs u ON u.id = t.auteur_id
            WHERE r.utilisateur_id = :user_id
            ORDER BY t.date_depart ASC
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'user_id' => $userId,
        ]);

        /** @var array<int, array<string, mixed>> $reservations */
        $reservations = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $reservations;
    }

    /**
     * Vérifie si l'utilisateur a déjà réservé ce trajet.
     */
    public function existsForUserAndTrip(int $userId, int $tripId): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT id FROM reservations WHERE utilisateur_id = :user_id AND trajet_id = :trip_id LIMIT 1'
        );
        $statement->execute([
            'user_id' => $userId,
            'trip_id' => $tripId,
        ]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * Réserve une place sur un trajet.
     */
    public function create(int $userId, int $tripId): bool
    {
        try {
            $this->pdo->beginTransaction();

            $trip = $this->lockTrip($tripId);
            if ($trip === null) {
                $this->pdo->rollBack();
                return false;
            }

            if ((int) $trip['auteur_id'] === $userId) {
                $this->pdo->rollBack();
                return false;
            }

            if ((int) $trip['places_disponibles'] <= 0) {
                $this->pdo->rollBack();
                return false;
            }

            if ($this->lockReservation($userId, $tripId) !== null) {
                $this->pdo->rollBack();
                return false;
            }

            $insert = $this->pdo->prepare(
                'INSERT INTO reservations (utilisateur_id, trajet_id) VALUES (:user_id, :trip_id)'
            );
            $insert->execute([
                'user_id' => $userId,
                'trip_id' => $tripId,
            ]);

            $updateTrip = $this->pdo->prepare(
                'UPDATE trajets SET places_disponibles = places_disponibles - 1 WHERE id = :trip_id'
            );
            $updateTrip->execute([
                'trip_id' => $tripId,
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return false;
        }
    }

    /**
     * Annule une réservation et libère une place.
     */
    public function delete(int $reservationId, int $userId): bool
    {
        try {
            $this->pdo->beginTransaction();

            $reservation = $this->lockReservationById($reservationId, $userId);
            if ($reservation === null) {
                $this->pdo->rollBack();
                return false;
            }

            $trip = $this->lockTrip((int) $reservation['trajet_id']);
            if ($trip === null) {
                $this->pdo->rollBack();
                return false;
            }

            $delete = $this->pdo->prepare(
                'DELETE FROM reservations WHERE id = :id AND utilisateur_id = :user_id'
            );
            $delete->execute([
                'id' => $reservationId,
                'user_id' => $userId,
            ]);

            if ($delete->rowCount() !== 1) {
                $this->pdo->rollBack();
                return false;
            }

            $updateTrip = $this->pdo->prepare(
                'UPDATE trajets SET places_disponibles = places_disponibles + 1 WHERE id = :trip_id'
            );
            $updateTrip->execute([
                'trip_id' => (int) $reservation['trajet_id'],
            ]);

            $this->pdo->commit();
            return true;
        } catch (Throwable $throwable) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return false;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function lockTrip(int $tripId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, auteur_id, places_total, places_disponibles FROM trajets WHERE id = :id LIMIT 1 FOR UPDATE'
        );
        $statement->execute([
            'id' => $tripId,
        ]);

        $trip = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($trip) ? $trip : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function lockReservation(int $userId, int $tripId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id FROM reservations WHERE utilisateur_id = :user_id AND trajet_id = :trip_id LIMIT 1 FOR UPDATE'
        );
        $statement->execute([
            'user_id' => $userId,
            'trip_id' => $tripId,
        ]);

        $reservation = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($reservation) ? $reservation : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function lockReservationById(int $reservationId, int $userId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT id, trajet_id, utilisateur_id FROM reservations WHERE id = :id AND utilisateur_id = :user_id LIMIT 1 FOR UPDATE'
        );
        $statement->execute([
            'id' => $reservationId,
            'user_id' => $userId,
        ]);

        $reservation = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($reservation) ? $reservation : null;
    }
}