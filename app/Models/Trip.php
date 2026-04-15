<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Trip
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

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

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $tripId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findAgencies(): array
    {
        return $this->pdo
            ->query("SELECT id, nom FROM agences ORDER BY nom")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

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

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

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

        $data["id"] = $tripId;
        $data["auteur_id"] = $authorId;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $stmt->rowCount() > 0;
    }
}