<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * ModÃƒÆ’Ã‚Â¨le des trajets.
 */
final class Trip
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Retourne les trajets disponibles pour la page d'accueil.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAvailableTrips(): array
    {
        $sql = <<<SQL
            SELECT
                t.id,
                agence_depart.nom AS departure_agency,
                agence_arrivee.nom AS arrival_agency,
                t.date_depart AS departure_datetime,
                t.date_arrivee AS arrival_datetime,
                t.places_disponibles AS available_seats
            FROM trajets t
            INNER JOIN agences agence_depart ON agence_depart.id = t.agence_depart_id
            INNER JOIN agences agence_arrivee ON agence_arrivee.id = t.agence_arrivee_id
            WHERE t.places_disponibles > 0
              AND t.date_depart >= NOW()
            ORDER BY t.date_depart ASC
        SQL;

        $statement = $this->pdo->query($sql);

        /** @var array<int, array<string, mixed>> $trips */
        $trips = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $trips;
    }

/**
 * Retourne un trajet par son identifiant.
 *
 * @return array<string, mixed>|null
 */
public function findById(int $tripId): ?array
{
    $sql = <<<SQL
        SELECT
            t.id,
            t.auteur_id,
            t.agence_depart_id,
            t.agence_arrivee_id,
            t.date_depart,
            t.date_arrivee,
            t.places_total,
            t.places_disponibles,
            ad.nom AS departure_agency,
            aa.nom AS arrival_agency,
            u.nom AS user_last_name,
            u.prenom AS user_first_name,
            u.email AS user_email,
            u.telephone AS user_phone
        FROM trajets t
        INNER JOIN agences ad ON ad.id = t.agence_depart_id
        INNER JOIN agences aa ON aa.id = t.agence_arrivee_id
        INNER JOIN utilisateurs u ON u.id = t.auteur_id
        WHERE t.id = :id
        LIMIT 1
    SQL;

    $statement = $this->pdo->prepare($sql);
    $statement->execute([
        'id' => $tripId,
    ]);

    $trip = $statement->fetch(PDO::FETCH_ASSOC);

    return is_array($trip) ? $trip : null;
}

    /**
     * Retourne le dÃƒÆ’Ã‚Â©tail d'un trajet pour un utilisateur connectÃƒÆ’Ã‚Â©.
     *
     * @param int $tripId
     * @return array<string, mixed>|null
     */
    public function findByIdForConnectedUser(int $tripId): ?array
    {
        $sql = <<<SQL
            SELECT
                t.id,
                agence_depart.nom AS departure_agency,
                agence_arrivee.nom AS arrival_agency,
                t.date_depart AS departure_datetime,
                t.date_arrivee AS arrival_datetime,
                t.places_total AS total_seats,
                t.places_disponibles AS available_seats,
                u.prenom AS first_name,
                u.nom AS last_name,
                u.telephone AS phone,
                u.email
            FROM trajets t
            INNER JOIN agences agence_depart ON agence_depart.id = t.agence_depart_id
            INNER JOIN agences agence_arrivee ON agence_arrivee.id = t.agence_arrivee_id
            INNER JOIN utilisateurs u ON u.id = t.auteur_id
            WHERE t.id = :id
            LIMIT 1
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $tripId, PDO::PARAM_INT);
        $statement->execute();

        $trip = $statement->fetch(PDO::FETCH_ASSOC);

        if ($trip === false) {
            return null;
        }

        /** @var array<string, mixed> $trip */
        return $trip;
    }

/**
 * CrÃ©e un trajet.
 *
 * @param array<string, mixed> $data
 */
public function create(array $data): void
{
    $sql = <<<SQL
        INSERT INTO trajets (
            auteur_id,
            agence_depart_id,
            agence_arrivee_id,
            date_depart,
            date_arrivee,
            places_total,
            places_disponibles
        ) VALUES (
            :auteur_id,
            :agence_depart_id,
            :agence_arrivee_id,
            :date_depart,
            :date_arrivee,
            :places_total,
            :places_disponibles
        )
    SQL;

    $statement = $this->pdo->prepare($sql);

    $statement->execute([
        'auteur_id' => (int) $data['auteur_id'],
        'agence_depart_id' => (int) $data['agence_depart_id'],
        'agence_arrivee_id' => (int) $data['agence_arrivee_id'],
        'date_depart' => (string) $data['date_depart'],
        'date_arrivee' => (string) $data['date_arrivee'],
        'places_total' => (int) $data['places_total'],
        'places_disponibles' => (int) $data['places_disponibles'],
    ]);
}

}
