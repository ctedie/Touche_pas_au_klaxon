<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Trip;
use App\Services\TripService;
use Tests\DatabaseTestCase;

final class TripWriteTest extends DatabaseTestCase
{
    public function testCreateTripPersistsExpectedData(): void
    {
        $service = new TripService(new Trip($this->pdo), new Reservation($this->pdo));

        $tripId = $service->createTrip([
            'agence_depart_id' => 1,
            'agence_arrivee_id' => 2,
            'date_depart' => '2030-07-10T08:15',
            'date_arrivee' => '2030-07-10T11:45',
            'places_total' => 4,
            'places_disponibles' => 3,
        ], 2);

        $trip = $this->fetchTrip($tripId);

        $this->assertNotNull($trip);
        $this->assertSame(2, (int) $trip['auteur_id']);
        $this->assertSame(1, (int) $trip['agence_depart_id']);
        $this->assertSame(2, (int) $trip['agence_arrivee_id']);
        $this->assertSame('2030-07-10 08:15:00', (string) $trip['date_depart']);
        $this->assertSame('2030-07-10 11:45:00', (string) $trip['date_arrivee']);
        $this->assertSame(4, (int) $trip['places_total']);
        $this->assertSame(3, (int) $trip['places_disponibles']);
    }

    public function testUpdateTripChangesPersistedValues(): void
    {
        $service = new TripService(new Trip($this->pdo), new Reservation($this->pdo));
        $tripId = $this->createTripFixture(authorId: 2);

        $updated = $service->updateTrip($tripId, 2, [
            'agence_depart_id' => 3,
            'agence_arrivee_id' => 4,
            'date_depart' => '2030-08-01T09:00',
            'date_arrivee' => '2030-08-01T13:30',
            'places_total' => 5,
            'places_disponibles' => 2,
        ]);

        $trip = $this->fetchTrip($tripId);

        $this->assertTrue($updated);
        $this->assertNotNull($trip);
        $this->assertSame(3, (int) $trip['agence_depart_id']);
        $this->assertSame(4, (int) $trip['agence_arrivee_id']);
        $this->assertSame('2030-08-01 09:00:00', (string) $trip['date_depart']);
        $this->assertSame('2030-08-01 13:30:00', (string) $trip['date_arrivee']);
        $this->assertSame(5, (int) $trip['places_total']);
        $this->assertSame(2, (int) $trip['places_disponibles']);
    }

    public function testDeleteTripRemovesPersistedRow(): void
    {
        $service = new TripService(new Trip($this->pdo), new Reservation($this->pdo));
        $tripId = $this->createTripFixture(authorId: 2);

        $deleted = $service->deleteTrip($tripId, 2);

        $this->assertTrue($deleted);
        $this->assertNull($this->fetchTrip($tripId));
        $this->assertSame(0, $this->countRows('trajets'));
    }
}