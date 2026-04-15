<?php

declare(strict_types=1);

namespace Tests;

use App\Core\Database;
use PDO;
use PHPUnit\Framework\TestCase as PhpUnitTestCase;
use Tests\Support\TestDatabase;

/**
 * Base commune pour les tests écrivant en base.
 */
abstract class DatabaseTestCase extends PhpUnitTestCase
{
    protected PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdo = TestDatabase::resetDatabase();
        Database::setConnection($this->pdo);
    }

    protected function tearDown(): void
    {
        Database::resetConnection();

        parent::tearDown();
    }

    protected function createTripFixture(
        int $authorId = 1,
        int $departureAgencyId = 1,
        int $arrivalAgencyId = 2,
        string $departureDate = '2030-06-01 08:00:00',
        string $arrivalDate = '2030-06-01 12:00:00',
        int $totalSeats = 4,
        int $availableSeats = 3
    ): int {
        $statement = $this->pdo->prepare(
            'INSERT INTO trajets (
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
            )'
        );

        $statement->execute([
            'auteur_id' => $authorId,
            'agence_depart_id' => $departureAgencyId,
            'agence_arrivee_id' => $arrivalAgencyId,
            'date_depart' => $departureDate,
            'date_arrivee' => $arrivalDate,
            'places_total' => $totalSeats,
            'places_disponibles' => $availableSeats,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    protected function createReservationFixture(int $userId, int $tripId): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO reservations (utilisateur_id, trajet_id) VALUES (:utilisateur_id, :trajet_id)'
        );
        $statement->execute([
            'utilisateur_id' => $userId,
            'trajet_id' => $tripId,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function fetchTrip(int $tripId): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM trajets WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $tripId]);

        $trip = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($trip) ? $trip : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function fetchReservation(int $reservationId): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM reservations WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $reservationId]);

        $reservation = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($reservation) ? $reservation : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function fetchReservationByUserAndTrip(int $userId, int $tripId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT * FROM reservations WHERE utilisateur_id = :user_id AND trajet_id = :trip_id LIMIT 1'
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
    protected function fetchAgency(int $agencyId): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM agences WHERE id = :id LIMIT 1');
        $statement->execute(['id' => $agencyId]);

        $agency = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($agency) ? $agency : null;
    }

    protected function countRows(string $tableName): int
    {
        $allowedTables = ['utilisateurs', 'agences', 'trajets', 'reservations'];

        if (!in_array($tableName, $allowedTables, true)) {
            $this->fail('Table non autorisée pour le comptage dans les tests.');
        }

        $statement = $this->pdo->query(sprintf('SELECT COUNT(*) FROM %s', $tableName));

        return (int) $statement->fetchColumn();
    }
}