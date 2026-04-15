<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\TripService;

/**
 * ContrÃ´leur des trajets.
 */
final class TripController extends Controller
{
    private TripService $tripService;

    public function __construct()
    {
        $this->tripService = new TripService();
    }

    /**
     * Affiche le dÃ©tail d'un trajet.
     */
    public function show(): void
    {
        $user = $this->requireAuthenticatedUser();

        $tripId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($tripId <= 0) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        $trip = $this->tripService->getTripById($tripId);
        if ($trip === null) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        $hasReservation = $this->tripService->hasUserReservedTrip((int) $user['id'], $tripId);

        $this->render('trips/show', [
            'trip' => $trip,
            'currentUser' => $user,
            'hasReservation' => $hasReservation,
        ]);
    }

    /**
     * Affiche le formulaire de crÃ©ation.
     */
    public function create(): void
    {
        $user = $this->requireAuthenticatedUser();

        $this->render('trips/create', [
            'agencies' => $this->tripService->getAgencies(),
            'errors' => [],
            'formData' => [
                'agence_depart_id' => 0,
                'agence_arrivee_id' => 0,
                'date_depart_form' => '',
                'date_arrivee_form' => '',
                'places_total' => 1,
                'places_disponibles' => 1,
            ],
            'currentUser' => $user,
        ]);
    }

    /**
     * Enregistre un trajet.
     */
    public function store(): void
    {
        $user = $this->requireAuthenticatedUser();

        $validation = $this->tripService->validateTripForm($_POST);

        if ($validation['errors'] !== []) {
            $this->render('trips/create', [
                'agencies' => $this->tripService->getAgencies(),
                'errors' => $validation['errors'],
                'formData' => $validation['data'],
                'currentUser' => $user,
            ]);
            return;
        }

        $tripId = $this->tripService->createTrip($validation['data'], (int) $user['id']);

        $_SESSION['flash_success'] = 'Le trajet a bien Ã©tÃ© crÃ©Ã©.';
        $this->redirect('/trip/show?id=' . $tripId);
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(): void
    {
        $user = $this->requireAuthenticatedUser();

        $tripId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($tripId <= 0) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        $trip = $this->tripService->getTripById($tripId);
        if ($trip === null) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        if ((int) $trip['author_id'] !== (int) $user['id']) {
            http_response_code(403);
            echo 'Vous nâ€™Ãªtes pas autorisÃ© Ã  modifier ce trajet.';
            return;
        }

        $this->render('trips/edit', [
            'trip' => $trip,
            'agencies' => $this->tripService->getAgencies(),
            'errors' => [],
            'formData' => $this->tripService->prepareTripFormData($trip),
            'currentUser' => $user,
        ]);
    }

    /**
     * Met Ã  jour un trajet.
     */
    public function update(): void
    {
        $user = $this->requireAuthenticatedUser();

        $tripId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($tripId <= 0) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        $trip = $this->tripService->getTripById($tripId);
        if ($trip === null) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        if ((int) $trip['author_id'] !== (int) $user['id']) {
            http_response_code(403);
            echo 'Vous nâ€™Ãªtes pas autorisÃ© Ã  modifier ce trajet.';
            return;
        }

        $validation = $this->tripService->validateTripForm($_POST);

        if ($validation['errors'] !== []) {
            $this->render('trips/edit', [
                'trip' => $trip,
                'agencies' => $this->tripService->getAgencies(),
                'errors' => $validation['errors'],
                'formData' => $validation['data'],
                'currentUser' => $user,
            ]);
            return;
        }

        $this->tripService->updateTrip($tripId, (int) $user['id'], $validation['data']);

        $_SESSION['flash_success'] = 'Le trajet a bien Ã©tÃ© modifiÃ©.';
        $this->redirect('/trip/show?id=' . $tripId);
    }

    /**
     * Supprime un trajet.
     */
    public function delete(): void
    {
        $user = $this->requireAuthenticatedUser();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/');
        }

        $tripId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
        if ($tripId <= 0) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        $trip = $this->tripService->getTripById($tripId);
        if ($trip === null) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        if ((int) $trip['author_id'] !== (int) $user['id']) {
            http_response_code(403);
            echo 'Vous nâ€™Ãªtes pas autorisÃ© Ã  supprimer ce trajet.';
            return;
        }

        $this->tripService->deleteTrip($tripId, (int) $user['id']);

        $_SESSION['flash_success'] = 'Le trajet a bien Ã©tÃ© supprimÃ©.';
        $this->redirect('/');
    }

    /**
     * RÃ©serve une place sur un trajet.
     */
    public function reserve(): void
    {
        $user = $this->requireAuthenticatedUser();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/');
        }

        $tripId = isset($_POST['trip_id']) ? (int) $_POST['trip_id'] : 0;
        if ($tripId <= 0) {
            http_response_code(404);
            echo 'Trajet introuvable.';
            return;
        }

        $error = $this->tripService->reserveTrip($tripId, (int) $user['id']);

        if ($error !== '') {
            $_SESSION['flash_error'] = $error;
            $this->redirect('/trip/show?id=' . $tripId);
        }

        $_SESSION['flash_success'] = 'Votre rÃ©servation a bien Ã©tÃ© enregistrÃ©e.';
        $this->redirect('/reservations');
    }

    /**
     * Affiche les rÃ©servations de l'utilisateur connectÃ©.
     */
    public function reservations(): void
    {
        $user = $this->requireAuthenticatedUser();

        $this->render('reservations/index', [
            'currentUser' => $user,
            'reservations' => $this->tripService->getReservationsByUserId((int) $user['id']),
        ]);
    }

    /**
     * Annule une rÃ©servation.
     */
    public function cancelReservation(): void
    {
        $user = $this->requireAuthenticatedUser();

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->redirect('/reservations');
        }

        $reservationId = isset($_POST['reservation_id']) ? (int) $_POST['reservation_id'] : 0;
        if ($reservationId <= 0) {
            http_response_code(404);
            echo 'RÃ©servation introuvable.';
            return;
        }

        $error = $this->tripService->cancelReservation($reservationId, (int) $user['id']);

        if ($error !== '') {
            $_SESSION['flash_error'] = $error;
            $this->redirect('/reservations');
        }

        $_SESSION['flash_success'] = 'La rÃ©servation a bien Ã©tÃ© annulÃ©e.';
        $this->redirect('/reservations');
    }

    /**
     * Retourne l'utilisateur connectÃ©.
     *
     * @return array<string, mixed>
     */
    private function requireAuthenticatedUser(): array
    {
        $user = $_SESSION['user'] ?? null;

        if (!is_array($user) || !isset($user['id'])) {
            $this->redirect('/login');
        }

        return [
            'id' => (int) ($user['id'] ?? 0),
            'first_name' => (string) ($user['first_name'] ?? $user['prenom'] ?? ''),
            'last_name' => (string) ($user['last_name'] ?? $user['nom'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'phone' => (string) ($user['phone'] ?? $user['telephone'] ?? ''),
        ];
    }
}
