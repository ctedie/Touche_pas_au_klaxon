<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Session;
use App\Models\Trip;

/**
 * Gère les trajets.
 */
final class TripController extends Controller
{
    /**
     * Affiche le détail d'un trajet pour un utilisateur connecté.
     */
    public function show(): void
    {
        $this->requireAuth();

        $tripId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        if ($tripId === false || $tripId === null) {
            Session::flash('error', 'Trajet introuvable.');
            $this->redirect('');
        }

        $tripModel = new Trip();
        $trip = $tripModel->findByIdForConnectedUser($tripId);

        if ($trip === null) {
            Session::flash('error', 'Trajet introuvable.');
            $this->redirect('');
        }

        $this->render('trip/show', [
            'pageTitle' => 'Détail du trajet',
            'trip' => $trip,
        ]);
    }
}