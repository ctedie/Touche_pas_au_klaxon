<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Modèle métier pour les trajets.
 */
final class Trip
{
    /**
     * Retourne les trajets futurs avec places disponibles.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function findAvailableTrips(): array
    {
        $pdo = Database::getConnection();

        $sql = "
            SELECT
                t.id,
                t.date_depart,
                t.date_arrivee,
                t.places_total,
                t.places_disponibles,

                ad.nom AS agence_depart,
                aa.nom AS agence_arrivee,

                u.nom,
                u.prenom,
                u.email,
                u.telephone

            FROM trajets t

            JOIN agences ad
                ON ad.id = t.agence_depart_id

            JOIN agences aa
                ON aa.id = t.agence_arrivee_id

            JOIN utilisateurs u
                ON u.id = t.auteur_id

            WHERE
                t.date_depart > NOW()
                AND t.places_disponibles > 0

            ORDER BY
                t.date_depart ASC
        ";

        $stmt = $pdo->query($sql);

        return $stmt->fetchAll();
    }
}