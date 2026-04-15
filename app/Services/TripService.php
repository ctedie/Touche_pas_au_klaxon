<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Trip;
use DateTimeImmutable;

/**
 * Service mÃ©tier des trajets.
 */
final class TripService
{
    private Trip $tripModel;

    public function __construct()
    {
        $this->tripModel = new Trip();
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
        }

        if ($arrivalDate === null) {
            $errors['date_arrivee'] = 'Veuillez saisir une date dâ€™arrivÃ©e valide.';
        }

        if ($departureDate !== null && $departureDate < $now) {
            $errors['date_depart'] = 'La date de dÃ©part doit Ãªtre dans le futur.';
        }

        if ($departureDate !== null && $arrivalDate !== null && $arrivalDate <= $departureDate) {
            $errors['date_arrivee'] = 'La date dâ€™arrivÃ©e doit Ãªtre postÃ©rieure Ã  la date de dÃ©part.';
        }

        if ($data['places_total'] <= 0) {
            $errors['places_total'] = 'Le nombre total de places doit Ãªtre supÃ©rieur Ã  0.';
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

        if ($departureDate !== null) {
            $data['date_depart'] = $departureDate->format('Y-m-d H:i:s');
        }

        if ($arrivalDate !== null) {
            $data['date_arrivee'] = $arrivalDate->format('Y-m-d H:i:s');
        }

        return [
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public function createTrip(array $data, int $userId): int
    {
        $data['auteur_id'] = $userId;

        return $this->tripModel->create($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function updateTrip(int $tripId, int $userId, array $data): bool
    {
        return $this->tripModel->update($tripId, $userId, $data);
    }

    public function deleteTrip(int $tripId, int $userId): bool
    {
        return $this->tripModel->delete($tripId, $userId);
    }

    /**
     * @param array<string, mixed> $trip
     * @return array<string, mixed>
     */
    public function prepareTripFormData(array $trip): array
    {
        return [
            'agence_depart_id' => (int) ($trip['agence_depart_id'] ?? 0),
            'agence_arrivee_id' => (int) ($trip['agence_arrivee_id'] ?? 0),
            'date_depart' => $this->formatForDateTimeLocal($trip['date_depart'] ?? null),
            'date_arrivee' => $this->formatForDateTimeLocal($trip['date_arrivee'] ?? null),
            'places_total' => (int) ($trip['places_total'] ?? 1),
            'places_disponibles' => (int) ($trip['places_disponibles'] ?? 1),
        ];
    }

    private function createDateTime(string $value): ?DateTimeImmutable
    {
        if ($value === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $value);

        if ($date instanceof DateTimeImmutable) {
            return $date;
        }

        return new DateTimeImmutable($value);
    }

    private function formatForDateTimeLocal(mixed $value): string
    {
        if (!is_string($value) || $value === '') {
            return '';
        }

        $date = new DateTimeImmutable($value);

        return $date->format('Y-m-d\TH:i');
    }
}