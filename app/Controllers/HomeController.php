<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Trip;

/**
 * Page d'accueil.
 */
class HomeController extends Controller
{
    /**
     * Affiche les trajets disponibles.
     */
    public function index(): void
    {
        $tripModel = new Trip();

        $trips = $tripModel->findAvailableTrips();

        $this->render('home/index', [
            'trips' => $trips
        ]);
    }
}
