<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Trip;
use DateTimeImmutable;

/**
 * Service mÃƒÂ©tier des trajets.
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
     * Retourne le dÃƒÂ©tail d'un trajet.
     *
     * @param int $tripId
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
     * Valide et normalise les donnÃƒÂ©es du formulaire.
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
            $errors['agence_depart_id'] = 'Veuillez sÃƒÂ©lectionner une agence de dÃƒÂ©part.';
        }

        if ($data['agence_arrivee_id'] <= 0) {
            $errors['agence_arrivee_id'] = 'Veuillez sÃƒÂ©lectionner une agence dÃ¢â‚¬â„¢arrivÃƒÂ©e.';
        }

        if (
            $data['agence_depart_id'] > 0
            && $data['agence_arrivee_id'] > 0
            && $data['agence_depart_id'] === $data['agence_arrivee_id']
        ) {
            $errors['agence_arrivee_id'] = 'LÃ¢â‚¬â„¢agence dÃ¢â‚¬â„¢arrivÃƒÂ©e doit ÃƒÂªtre diffÃƒÂ©rente de lÃ¢â‚¬â„¢agence de dÃƒÂ©part.';
        }

        $departureDate = $this->createDateTime($data['date_depart']);
        $arrivalDate = $this->createDateTime($data['date_arrivee']);
        $now = new DateTimeImmutable();

        if ($departureDate === null) {
            $errors['date_depart'] = 'Veuillez saisir une date de dÃƒÂ©part valide.';
        }

        if ($arrivalDate === null) {
            $errors['date_arrivee'] = 'Veuillez saisir une date dÃ¢â‚¬â„¢arrivÃƒÂ©e valide.';
        }

        if ($departureDate !== null && $departureDate < $now) {
            $errors['date_depart'] = 'La date de dÃƒÂ©part doit ÃƒÂªtre dans le futur.';
        }

        if ($departureDate !== null && $arrivalDate !== null && $arrivalDate <= $departureDate) {
            $errors['date_arrivee'] = 'La date dÃ¢â‚¬â„¢arrivÃƒÂ©e doit ÃƒÂªtre postÃƒÂ©rieure ÃƒÂ  la date de dÃƒÂ©part.';
        }

        if ($data['places_total'] <= 0) {
            $errors['places_total'] = 'Le nombre total de places doit ÃƒÂªtre supÃƒÂ©rieur ÃƒÂ  0.';
        }

        if ($data['places_disponibles'] < 0) {
            $errors['places_disponibles'] = 'Le nombre de places disponibles ne peut pas ÃƒÂªtre nÃƒÂ©gatif.';
        }

        if (
            $data['places_total'] > 0
            && $data['places_disponibles'] > $data['places_total']
        ) {
            $errors['places_disponibles'] = 'Le nombre de places disponibles ne peut pas dÃƒÂ©passer le nombre total de places.';
        }

        if ($departureDate !== null) {
            $data['date_depart'] = $departureDate->format('Y-m-d H:i:s');
            $data['date_depart_form'] = $departureDate->format('Y-m-d\TH:i');
        } else {
            $data['date_depart_form'] = trim((string) ($input['date_depart'] ?? ''));
        }

        if ($arrivalDate !== null) {
            $data['date_arrivee'] = $arrivalDate->format('Y-m-d H:i:s');
            $data['date_arrivee_form'] = $arrivalDate->format('Y-m-d\TH:i');
        } else {
            $data['date_arrivee_form'] = trim((string) ($input['date_arrivee'] ?? ''));
        }

        return [
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /**
     * CrÃƒÂ©e un trajet.
     *
     * @param array<string, mixed> $validatedData
     * @param int $userId
     * @return int
     */
    public function createTrip(array $validatedData, int $userId): int
    {
        $payload = [
            'auteur_id' => $userId,
            'agence_depart_id' => (int) $validatedData['agence_depart_id'],
            'agence_arrivee_id' => (int) $validatedData['agence_arrivee_id'],
            'date_depart' => (string) $validatedData['date_depart'],
            'date_arrivee' => (string) $validatedData['date_arrivee'],
            'places_total' => (int) $validatedData['places_total'],
            'places_disponibles' => (int) $validatedData['places_disponibles'],
        ];

        return $this->tripModel->create($payload);
    }

    /**
     * Met ÃƒÂ  jour un trajet.
     *
     * @param int $tripId
     * @param int $userId
     * @param array<string, mixed> $validatedData
     * @return bool
     */
    public function updateTrip(int $tripId, int $userId, array $validatedData): bool
    {
        $payload = [
            'agence_depart_id' => (int) $validatedData['agence_depart_id'],
            'agence_arrivee_id' => (int) $validatedData['agence_arrivee_id'],
            'date_depart' => (string) $validatedData['date_depart'],
            'date_arrivee' => (string) $validatedData['date_arrivee'],
            'places_total' => (int) $validatedData['places_total'],
            'places_disponibles' => (int) $validatedData['places_disponibles'],
        ];

        return $this->tripModel->update($tripId, $userId, $payload);
    }

    /**
     * PrÃƒÂ©pare les donnÃƒÂ©es d'un trajet pour le formulaire.
     *
     * @param array<string, mixed> $trip
     * @return array<string, mixed>
     */
    public function prepareTripFormData(array $trip): array
    {
        return [
            'agence_depart_id' => (int) ($trip['agence_depart_id'] ?? 0),
            'agence_arrivee_id' => (int) ($trip['agence_arrivee_id'] ?? 0),
            'date_depart_form' => $this->formatDateTimeForForm((string) ($trip['date_depart'] ?? '')),
            'date_arrivee_form' => $this->formatDateTimeForForm((string) ($trip['date_arrivee'] ?? '')),
            'places_total' => (int) ($trip['places_total'] ?? 0),
            'places_disponibles' => (int) ($trip['places_disponibles'] ?? 0),
        ];
    }

    /**
     * Convertit une date HTML datetime-local en DateTimeImmutable.
     *
     * @param string $value
     * @return DateTimeImmutable|null
     */
    private function createDateTime(string $value): ?DateTimeImmutable
    {
        if ($value === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $value);

        return $date instanceof DateTimeImmutable ? $date : null;
    }

    /**
     * Formate une date SQL pour un champ datetime-local.
     *
     * @param string $value
     * @return string
     */
    private function formatDateTimeForForm(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $date = new DateTimeImmutable($value);

        return $date->format('Y-m-d\TH:i');
    }
}
