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
     * Retourne le dÃ©tail d'un trajet pour un utilisateur connectÃ©.
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
}