<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Trip;
use App\Services\TripService;
use Tests\DatabaseTestCase;

final class ReservationWriteTest extends DatabaseTestCase
{
    public function testReserveTripCreatesReservationAndDecrementsAvailableSeats(): void
    {
        $tripId = $this->createTripFixture(authorId: 1, availableSeats: 3);
        $service = new TripService(new Trip($this->pdo), new Reservation($this->pdo));

        $error = $service->reserveTrip($tripId, 2);

        $trip = $this->fetchTrip($tripId);
        $reservation = $this->fetchReservationByUserAndTrip(2, $tripId);

        $this->assertSame('', $error);
        $this->assertNotNull($trip);
        $this->assertNotNull($reservation);
        $this->assertSame(2, (int) $trip['places_disponibles']);
        $this->assertSame(1, $this->countRows('reservations'));
    }

    public function testCancelReservationDeletesReservationAndIncrementsAvailableSeats(): void
    {
        $tripId = $this->createTripFixture(authorId: 1, availableSeats: 2);
        $reservationModel = new Reservation($this->pdo);
        $service = new TripService(new Trip($this->pdo), $reservationModel);

        $reservationError = $service->reserveTrip($tripId, 2);
        $reservation = $this->fetchReservationByUserAndTrip(2, $tripId);

        $this->assertSame('', $reservationError);
        $this->assertNotNull($reservation);

        $cancelError = $service->cancelReservation((int) $reservation['id'], 2);
        $trip = $this->fetchTrip($tripId);

        $this->assertSame('', $cancelError);
        $this->assertNotNull($trip);
        $this->assertSame(2, (int) $trip['places_disponibles']);
        $this->assertNull($this->fetchReservation((int) $reservation['id']));
        $this->assertSame(0, $this->countRows('reservations'));
    }
}