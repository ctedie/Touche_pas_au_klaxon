<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * ModÃ¨le des trajets.
 */
final class Trip
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    /**
     * Retourne les trajets disponibles pour la page d'accueil.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAvailableTrips(int $limit, int $offset): array
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
            LIMIT :limit OFFSET :offset
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        /** @var array<int, array<string, mixed>> $trips */
        $trips = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $trips;
    }

    /**
     * Retourne le nombre de trajets visibles sur l'accueil.
     */
    public function countAvailableTrips(): int
    {
        $sql = <<<SQL
            SELECT COUNT(*)
            FROM trajets t
            WHERE t.places_disponibles > 0
              AND t.date_depart >= NOW()
        SQL;

        $statement = $this->pdo->query($sql);

        if ($statement === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * Retourne le dÃ©tail d'un trajet.
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
                agence_depart.nom AS departure_agency,
                agence_arrivee.nom AS arrival_agency,
                u.id AS author_id,
                u.prenom AS author_first_name,
                u.nom AS author_last_name,
                u.email AS author_email,
                u.telephone AS author_phone
            FROM trajets t
            INNER JOIN agences agence_depart ON agence_depart.id = t.agence_depart_id
            INNER JOIN agences agence_arrivee ON agence_arrivee.id = t.agence_arrivee_id
            INNER JOIN utilisateurs u ON u.id = t.auteur_id
            WHERE t.id = :id
            LIMIT 1
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute(['id' => $tripId]);

        $trip = $statement->fetch(PDO::FETCH_ASSOC);

        if (!is_array($trip)) {
            return null;
        }

        /** @var array<string, mixed> $trip */
        return $trip;
    }

    /**
     * Retourne les agences.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAgencies(): array
    {
        $statement = $this->pdo->query('SELECT id, nom FROM agences ORDER BY nom ASC');

        if ($statement === false) {
            return [];
        }

        /** @var array<int, array<string, mixed>> $agencies */
        $agencies = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $agencies;
    }

    /**
     * Retourne la liste paginÃ©e des trajets pour l'administration.
     *
     * @return array<int, array<string, mixed>>
     */
    public function findAllForAdmin(int $limit, int $offset): array
    {
        $sql = <<<SQL
            SELECT
                t.id,
                ad.nom AS departure_agency,
                aa.nom AS arrival_agency,
                t.date_depart AS departure_datetime,
                t.date_arrivee AS arrival_datetime,
                t.places_total,
                t.places_disponibles AS available_seats,
                u.prenom AS author_first_name,
                u.nom AS author_last_name,
                u.email AS author_email
            FROM trajets t
            INNER JOIN agences ad ON ad.id = t.agence_depart_id
            INNER JOIN agences aa ON aa.id = t.agence_arrivee_id
            INNER JOIN utilisateurs u ON u.id = t.auteur_id
            WHERE t.places_disponibles > 0
              AND t.date_depart >= NOW()
            ORDER BY t.date_depart ASC
            LIMIT :limit OFFSET :offset
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        /** @var array<int, array<string, mixed>> $trips */
        $trips = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $trips;
    }

    /**
     * Retourne le nombre de trajets visibles dans l'administration.
     */
    public function countAllForAdmin(): int
    {
        $sql = <<<SQL
            SELECT COUNT(*)
            FROM trajets t
            WHERE t.places_disponibles > 0
              AND t.date_depart >= NOW()
        SQL;

        $statement = $this->pdo->query($sql);

        if ($statement === false) {
            return 0;
        }

        return (int) $statement->fetchColumn();
    }

    /**
     * CrÃ©e un trajet.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
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
            'auteur_id' => $data['auteur_id'],
            'agence_depart_id' => $data['agence_depart_id'],
            'agence_arrivee_id' => $data['agence_arrivee_id'],
            'date_depart' => $data['date_depart'],
            'date_arrivee' => $data['date_arrivee'],
            'places_total' => $data['places_total'],
            'places_disponibles' => $data['places_disponibles'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Met Ã  jour un trajet appartenant Ã  son auteur.
     *
     * @param array<string, mixed> $data
     */
    public function update(int $tripId, int $authorId, array $data): bool
    {
        $sql = <<<SQL
            UPDATE trajets
            SET
                agence_depart_id = :agence_depart_id,
                agence_arrivee_id = :agence_arrivee_id,
                date_depart = :date_depart,
                date_arrivee = :date_arrivee,
                places_total = :places_total,
                places_disponibles = :places_disponibles
            WHERE id = :id
              AND auteur_id = :auteur_id
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id' => $tripId,
            'auteur_id' => $authorId,
            'agence_depart_id' => $data['agence_depart_id'],
            'agence_arrivee_id' => $data['agence_arrivee_id'],
            'date_depart' => $data['date_depart'],
            'date_arrivee' => $data['date_arrivee'],
            'places_total' => $data['places_total'],
            'places_disponibles' => $data['places_disponibles'],
        ]);

        return $statement->rowCount() > 0;
    }

    /**
     * Supprime un trajet appartenant Ã  son auteur.
     */
    public function delete(int $tripId, int $authorId): bool
    {
        $sql = <<<SQL
            DELETE FROM trajets
            WHERE id = :id
              AND auteur_id = :auteur_id
        SQL;

        $statement = $this->pdo->prepare($sql);
        $statement->execute([
            'id' => $tripId,
            'auteur_id' => $authorId,
        ]);

        return $statement->rowCount() > 0;
    }

    /**
     * Supprime un trajet sans contrÃ´le d'auteur.
     * UtilisÃ© uniquement par l'administration.
     */
    public function deleteById(int $tripId): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM trajets WHERE id = :id');
        $statement->execute([
            'id' => $tripId,
        ]);

        return $statement->rowCount() > 0;
    }
}