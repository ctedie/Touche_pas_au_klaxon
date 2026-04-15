<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Reservation;
use App\Models\Trip;
use DateTimeImmutable;

/**
 * Service mÃ©tier des trajets.
 */
final class TripService
{
    private Trip $tripModel;

    private Reservation $reservationModel;

    public function __construct()
    {
        $this->tripModel = new Trip();
        $this->reservationModel = new Reservation();
    }

    /**
     * Retourne les trajets visibles sur l'accueil.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAvailableTrips(): array
    {
        return $this->tripModel->findAvailableTrips();
    }

    /**
     * Retourne le dÃ©tail d'un trajet.
     *
     * @return array<string, mixed>|null
     */
    public function getTripById(int $tripId): ?array
    {
        return $this->tripModel->findById($tripId);
    }

    /**
     * Retourne les agences.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAgencies(): array
    {
        return $this->tripModel->findAgencies();
    }

    /**
     * Retourne les rÃ©servations d'un utilisateur.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getReservationsByUserId(int $userId): array
    {
        return $this->reservationModel->findByUserId($userId);
    }

    /**
     * VÃ©rifie si l'utilisateur a dÃ©jÃ  rÃ©servÃ© ce trajet.
     */
    public function hasUserReservedTrip(int $userId, int $tripId): bool
    {
        return $this->reservationModel->existsForUserAndTrip($userId, $tripId);
    }

    /**
     * Valide et normalise les donnÃ©es du formulaire.
     *
     * @param array<string, mixed> $input
     * @return array{
     *     data: array<string, mixed>,
     *     errors: array<string, string>
     * }
     */
    public function validateTripForm(array $input): array
    {
        $data = [
            'agence_depart_id' => (int) ($input['agence_depart_id'] ?? 0),
            'agence_arrivee_id' => (int) ($input['agence_arrivee_id'] ?? 0),
            'date_depart' => trim((string) ($input['date_depart'] ?? '')),
            'date_arrivee' => trim((string) ($input['date_arrivee'] ?? '')),
            'places_total' => (int) ($input['places_total'] ?? 0),
            'places_disponibles' => (int) ($input['places_disponibles'] ?? 0),
        ];

        $errors = [];

        if ($data['agence_depart_id'] <= 0) {
            $errors['agence_depart_id'] = 'Veuillez sÃ©lectionner une agence de dÃ©part.';
        }

        if ($data['agence_arrivee_id'] <= 0) {
            $errors['agence_arrivee_id'] = 'Veuillez sÃ©lectionner une agence dâ€™arrivÃ©e.';
        }

        if (
            $data['agence_depart_id'] > 0
            && $data['agence_arrivee_id'] > 0
            && $data['agence_depart_id'] === $data['agence_arrivee_id']
        ) {
            $errors['agence_arrivee_id'] = 'Lâ€™agence dâ€™arrivÃ©e doit Ãªtre diffÃ©rente de lâ€™agence de dÃ©part.';
        }

        $departureDate = $this->createDateTime($data['date_depart']);
        $arrivalDate = $this->createDateTime($data['date_arrivee']);
        $now = new DateTimeImmutable();

        if ($departureDate === null) {
            $errors['date_depart'] = 'Veuillez saisir une date de dÃ©part valide.';
        } elseif ($departureDate < $now) {
            $errors['date_depart'] = 'La date de dÃ©part doit Ãªtre dans le futur.';
        }

        if ($arrivalDate === null) {
            $errors['date_arrivee'] = 'Veuillez saisir une date dâ€™arrivÃ©e valide.';
        }

        if ($departureDate !== null && $arrivalDate !== null && $arrivalDate <= $departureDate) {
            $errors['date_arrivee'] = 'La date dâ€™arrivÃ©e doit Ãªtre postÃ©rieure Ã  la date de dÃ©part.';
        }

        if ($data['places_total'] <= 0) {
            $errors['places_total'] = 'Le nombre total de places doit Ãªtre supÃ©rieur Ã  zÃ©ro.';
        }

        if ($data['places_disponibles'] < 0) {
            $errors['places_disponibles'] = 'Le nombre de places disponibles ne peut pas Ãªtre nÃ©gatif.';
        }

        if (
            $data['places_total'] > 0
            && $data['places_disponibles'] > $data['places_total']
        ) {
            $errors['places_disponibles'] = 'Le nombre de places disponibles ne peut pas dÃ©passer le nombre total de places.';
        }

        return [
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /**
     * PrÃ©pare les donnÃ©es pour le formulaire dâ€™Ã©dition.
     *
     * @param array<string, mixed> $trip
     * @return array<string, mixed>
     */
    public function prepareTripFormData(array $trip): array
    {
        return [
            'agence_depart_id' => (int) ($trip['agence_depart_id'] ?? 0),
            'agence_arrivee_id' => (int) ($trip['agence_arrivee_id'] ?? 0),
            'date_depart' => $this->formatForDatetimeLocal((string) ($trip['date_depart'] ?? '')),
            'date_arrivee' => $this->formatForDatetimeLocal((string) ($trip['date_arrivee'] ?? '')),
            'places_total' => (int) ($trip['places_total'] ?? 0),
            'places_disponibles' => (int) ($trip['places_disponibles'] ?? 0),
        ];
    }

    /**
     * CrÃ©e un trajet.
     *
     * @param array<string, mixed> $data
     */
    public function createTrip(array $data, int $authorId): int
    {
        $data['auteur_id'] = $authorId;
        $data['date_depart'] = $this->normalizeDateTimeForDatabase((string) $data['date_depart']);
        $data['date_arrivee'] = $this->normalizeDateTimeForDatabase((string) $data['date_arrivee']);

        return $this->tripModel->create($data);
    }

    /**
     * Met Ã  jour un trajet.
     *
     * @param array<string, mixed> $data
     */
    public function updateTrip(int $tripId, int $authorId, array $data): bool
    {
        $data['date_depart'] = $this->normalizeDateTimeForDatabase((string) $data['date_depart']);
        $data['date_arrivee'] = $this->normalizeDateTimeForDatabase((string) $data['date_arrivee']);

        return $this->tripModel->update($tripId, $authorId, $data);
    }

    /**
     * Supprime un trajet.
     */
    public function deleteTrip(int $tripId, int $authorId): bool
    {
        return $this->tripModel->delete($tripId, $authorId);
    }

    /**
     * Tente de rÃ©server une place.
     */
    public function reserveTrip(int $tripId, int $userId): string
    {
        $trip = $this->tripModel->findById($tripId);

        if ($trip === null) {
            return 'Trajet introuvable.';
        }

        if ((int) ($trip['author_id'] ?? 0) === $userId) {
            return 'Vous ne pouvez pas rÃ©server votre propre trajet.';
        }

        if ((int) ($trip['places_disponibles'] ?? 0) <= 0) {
            return 'Ce trajet est complet.';
        }

        if ($this->reservationModel->existsForUserAndTrip($userId, $tripId)) {
            return 'Vous avez dÃ©jÃ  rÃ©servÃ© une place sur ce trajet.';
        }

        if (!$this->reservationModel->create($userId, $tripId)) {
            return 'La rÃ©servation a Ã©chouÃ©. Veuillez rÃ©essayer.';
        }

        return '';
    }

    /**
     * Tente d'annuler une rÃ©servation.
     */
    public function cancelReservation(int $reservationId, int $userId): string
    {
        if (!$this->reservationModel->delete($reservationId, $userId)) {
            return 'RÃ©servation introuvable ou annulation impossible.';
        }

        return '';
    }

    private function createDateTime(string $value): ?DateTimeImmutable
    {
        if ($value === '') {
            return null;
        }

        $dateTime = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $value);

        if ($dateTime instanceof DateTimeImmutable) {
            return $dateTime;
        }

        $fallback = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value);

        return $fallback instanceof DateTimeImmutable ? $fallback : null;
    }

    private function normalizeDateTimeForDatabase(string $value): string
    {
        $dateTime = $this->createDateTime($value);

        return $dateTime instanceof DateTimeImmutable ? $dateTime->format('Y-m-d H:i:s') : $value;
    }

    private function formatForDatetimeLocal(string $value): string
    {
        $dateTime = $this->createDateTime($value);

        return $dateTime instanceof DateTimeImmutable ? $dateTime->format('Y-m-d\TH:i') : $value;
    }
}
