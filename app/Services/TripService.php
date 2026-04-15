<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Reservation;
use App\Models\Trip;
use DateTimeImmutable;

/**
 * Service métier des trajets.
 */
final class TripService
{
    private const DEFAULT_PER_PAGE = 5;

    private Trip $tripModel;

    private Reservation $reservationModel;

    public function __construct(?Trip $tripModel = null, ?Reservation $reservationModel = null)
    {
        $this->tripModel = $tripModel ?? new Trip();
        $this->reservationModel = $reservationModel ?? new Reservation();
    }

    /**
     * Retourne les trajets visibles sur l'accueil.
     *
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, int|bool>}
     */
    public function getAvailableTripsPage(int $page, int $perPage = self::DEFAULT_PER_PAGE): array
    {
        $pagination = $this->buildPagination($page, $perPage, $this->tripModel->countAvailableTrips());

        return [
            'items' => $this->tripModel->findAvailableTrips($pagination['per_page'], $pagination['offset']),
            'pagination' => $pagination,
        ];
    }

    /**
     * Retourne les trajets visibles dans l'administration.
     *
     * @return array{items: array<int, array<string, mixed>>, pagination: array<string, int|bool>}
     */
    public function getAdminTripsPage(int $page, int $perPage = self::DEFAULT_PER_PAGE): array
    {
        $pagination = $this->buildPagination($page, $perPage, $this->tripModel->countAllForAdmin());

        return [
            'items' => $this->tripModel->findAllForAdmin($pagination['per_page'], $pagination['offset']),
            'pagination' => $pagination,
        ];
    }

    /**
     * Retourne le détail d'un trajet.
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
     * Retourne les réservations d'un utilisateur.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getReservationsByUserId(int $userId): array
    {
        return $this->reservationModel->findByUserId($userId);
    }

    /**
     * Vérifie si l'utilisateur a déjà réservé ce trajet.
     */
    public function hasUserReservedTrip(int $userId, int $tripId): bool
    {
        return $this->reservationModel->existsForUserAndTrip($userId, $tripId);
    }

    /**
     * Valide et normalise les données du formulaire.
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
            $errors['agence_depart_id'] = 'Veuillez sélectionner une agence de départ.';
        }

        if ($data['agence_arrivee_id'] <= 0) {
            $errors['agence_arrivee_id'] = 'Veuillez sélectionner une agence d’arrivée.';
        }

        if (
            $data['agence_depart_id'] > 0
            && $data['agence_arrivee_id'] > 0
            && $data['agence_depart_id'] === $data['agence_arrivee_id']
        ) {
            $errors['agence_arrivee_id'] = 'L’agence d’arrivée doit être différente de l’agence de départ.';
        }

        $departureDate = $this->createDateTime($data['date_depart']);
        $arrivalDate = $this->createDateTime($data['date_arrivee']);
        $now = new DateTimeImmutable();

        if ($departureDate === null) {
            $errors['date_depart'] = 'Veuillez saisir une date de départ valide.';
        } elseif ($departureDate < $now) {
            $errors['date_depart'] = 'La date de départ doit être dans le futur.';
        }

        if ($arrivalDate === null) {
            $errors['date_arrivee'] = 'Veuillez saisir une date d’arrivée valide.';
        }

        if ($departureDate !== null && $arrivalDate !== null && $arrivalDate <= $departureDate) {
            $errors['date_arrivee'] = 'La date d’arrivée doit être postérieure à la date de départ.';
        }

        if ($data['places_total'] <= 0) {
            $errors['places_total'] = 'Le nombre total de places doit être supérieur à zéro.';
        }

        if ($data['places_disponibles'] < 0) {
            $errors['places_disponibles'] = 'Le nombre de places disponibles ne peut pas être négatif.';
        }

        if (
            $data['places_total'] > 0
            && $data['places_disponibles'] > $data['places_total']
        ) {
            $errors['places_disponibles'] = 'Le nombre de places disponibles ne peut pas dépasser le nombre total de places.';
        }

        return [
            'data' => $data,
            'errors' => $errors,
        ];
    }

    /**
     * Prépare les données pour le formulaire d’édition.
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
     * Crée un trajet.
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
     * Met à jour un trajet.
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
     * Tente de réserver une place.
     */
    public function reserveTrip(int $tripId, int $userId): string
    {
        $trip = $this->tripModel->findById($tripId);

        if ($trip === null) {
            return 'Trajet introuvable.';
        }

        if ((int) ($trip['author_id'] ?? 0) === $userId) {
            return 'Vous ne pouvez pas réserver votre propre trajet.';
        }

        if ((int) ($trip['places_disponibles'] ?? 0) <= 0) {
            return 'Ce trajet est complet.';
        }

        if ($this->reservationModel->existsForUserAndTrip($userId, $tripId)) {
            return 'Vous avez déjà réservé une place sur ce trajet.';
        }

        if (!$this->reservationModel->create($userId, $tripId)) {
            return 'La réservation a échoué. Veuillez réessayer.';
        }

        return '';
    }

    /**
     * Tente d'annuler une réservation.
     */
    public function cancelReservation(int $reservationId, int $userId): string
    {
        if (!$this->reservationModel->delete($reservationId, $userId)) {
            return 'Réservation introuvable ou annulation impossible.';
        }

        return '';
    }

    /**
     * @return array<string, int|bool>
     */
    private function buildPagination(int $page, int $perPage, int $totalItems): array
    {
        $safePerPage = max(1, $perPage);
        $totalPages = max(1, (int) ceil($totalItems / $safePerPage));
        $currentPage = min(max(1, $page), $totalPages);

        return [
            'current_page' => $currentPage,
            'per_page' => $safePerPage,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'offset' => ($currentPage - 1) * $safePerPage,
            'has_previous_page' => $currentPage > 1,
            'has_next_page' => $currentPage < $totalPages,
            'previous_page' => max(1, $currentPage - 1),
            'next_page' => min($totalPages, $currentPage + 1),
        ];
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