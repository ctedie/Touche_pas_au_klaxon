<?php

declare(strict_types=1);

namespace App\Controllers;

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
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $isConnected = isset($_SESSION['user']) && is_array($_SESSION['user']);

        if (!$isConnected) {
            http_response_code(403);
            $this->render('trip/show', [
                'trip' => null,
                'error' => 'Vous devez Ãªtre connectÃ© pour consulter le dÃ©tail dâ€™un trajet.',
            ]);
            return;
        }

        $tripId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($tripId === false || $tripId === null || $tripId <= 0) {
            http_response_code(400);
            $this->render('trip/show', [
                'trip' => null,
                'error' => 'Identifiant de trajet invalide.',
            ]);
            return;
        }

        $tripModel = new Trip();
        $trip = $tripModel->findByIdForConnectedUser($tripId);

        if ($trip === null) {
            http_response_code(404);
            $this->render('trip/show', [
                'trip' => null,
                'error' => 'Trajet introuvable.',
            ]);
            return;
        }

        $this->render('trip/show', [
            'trip' => $trip,
            'error' => null,
        ]);
    }
}