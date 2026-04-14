<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Agency;
use App\Models\Trip;

/**
 * ContrÃ´leur des trajets.
 */
final class TripController extends Controller
{
    /**
     * Affiche le dÃ©tail d'un trajet.
     */
    public function show(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('/login');
        }

        $tripId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($tripId <= 0) {
            $_SESSION['flash_error'] = 'Trajet introuvable.';
            $this->redirect('/');
        }

        $tripModel = new Trip();
        $trip = $tripModel->findById($tripId);

        if ($trip === null) {
            $_SESSION['flash_error'] = 'Trajet introuvable.';
            $this->redirect('/');
        }

        $this->render('trips/show', [
            'title' => 'DÃ©tail du trajet',
            'trip' => $trip,
        ]);
    }

    /**
     * Affiche le formulaire de crÃ©ation d'un trajet.
     */
    public function create(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('/login');
        }

        $agencyModel = new Agency();
        $agencies = $agencyModel->findAll();

        $this->render('trips/create', [
            'title' => 'CrÃ©er un trajet',
            'user' => $user,
            'agencies' => $agencies,
            'errors' => [],
            'old' => [],
        ]);
    }

    /**
     * Enregistre un nouveau trajet.
     */
    public function store(): void
    {
        $user = $this->getAuthenticatedUser();

        if ($user === null) {
            $this->redirect('/login');
        }

        $agencyModel = new Agency();
        $agencies = $agencyModel->findAll();

        $departureAgencyId = isset($_POST['departure_agency_id']) ? (int) $_POST['departure_agency_id'] : 0;
        $arrivalAgencyId = isset($_POST['arrival_agency_id']) ? (int) $_POST['arrival_agency_id'] : 0;
        $departureDatetime = isset($_POST['departure_datetime']) ? trim((string) $_POST['departure_datetime']) : '';
        $arrivalDatetime = isset($_POST['arrival_datetime']) ? trim((string) $_POST['arrival_datetime']) : '';
        $seatCount = isset($_POST['seat_count']) ? (int) $_POST['seat_count'] : 0;

        $old = [
            'departure_agency_id' => $departureAgencyId,
            'arrival_agency_id' => $arrivalAgencyId,
            'departure_datetime' => $departureDatetime,
            'arrival_datetime' => $arrivalDatetime,
            'seat_count' => $seatCount,
        ];

        $errors = $this->validateTripForm(
            $departureAgencyId,
            $arrivalAgencyId,
            $departureDatetime,
            $arrivalDatetime,
            $seatCount
        );

        if ($errors !== []) {
            $this->render('trips/create', [
                'title' => 'CrÃ©er un trajet',
                'user' => $user,
                'agencies' => $agencies,
                'errors' => $errors,
                'old' => $old,
            ]);

            return;
        }

        $tripModel = new Trip();
$tripModel->create([
    'auteur_id' => (int) $user['id'],
    'agence_depart_id' => $departureAgencyId,
    'agence_arrivee_id' => $arrivalAgencyId,
    'date_depart' => $departureDatetime,
    'date_arrivee' => $arrivalDatetime,
    'places_total' => $seatCount,
    'places_disponibles' => $seatCount,
]);

        $_SESSION['flash_success'] = 'Le trajet a bien Ã©tÃ© crÃ©Ã©.';

        $this->redirect('/');
    }

    /**
     * Valide le formulaire de crÃ©ation.
     *
     * @return array<string, string>
     */
    private function validateTripForm(
        int $departureAgencyId,
        int $arrivalAgencyId,
        string $departureDatetime,
        string $arrivalDatetime,
        int $seatCount
    ): array {
        $errors = [];

        if ($departureAgencyId <= 0) {
            $errors['departure_agency_id'] = 'Veuillez sÃ©lectionner une agence de dÃ©part.';
        }

        if ($arrivalAgencyId <= 0) {
            $errors['arrival_agency_id'] = 'Veuillez sÃ©lectionner une agence dâ€™arrivÃ©e.';
        }

        if ($departureAgencyId === $arrivalAgencyId && $departureAgencyId > 0) {
            $errors['arrival_agency_id'] = 'Lâ€™agence dâ€™arrivÃ©e doit Ãªtre diffÃ©rente de lâ€™agence de dÃ©part.';
        }

        $departureTimestamp = strtotime($departureDatetime);
        $arrivalTimestamp = strtotime($arrivalDatetime);

        if ($departureDatetime === '' || $departureTimestamp === false) {
            $errors['departure_datetime'] = 'Veuillez saisir une date et heure de dÃ©part valides.';
        }

        if ($arrivalDatetime === '' || $arrivalTimestamp === false) {
            $errors['arrival_datetime'] = 'Veuillez saisir une date et heure dâ€™arrivÃ©e valides.';
        }

        if (
            $departureTimestamp !== false
            && $arrivalTimestamp !== false
            && $arrivalTimestamp <= $departureTimestamp
        ) {
            $errors['arrival_datetime'] = 'La date et heure dâ€™arrivÃ©e doit Ãªtre postÃ©rieure Ã  la date et heure de dÃ©part.';
        }

        if ($seatCount <= 0) {
            $errors['seat_count'] = 'Le nombre de places doit Ãªtre supÃ©rieur Ã  0.';
        }

        return $errors;
    }
}