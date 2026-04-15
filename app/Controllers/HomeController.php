<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\TripService;

/**
 * Page d'accueil.
 */
final class HomeController extends Controller
{
    private const TRIPS_PER_PAGE = 5;

    /**
     * Affiche les trajets disponibles.
     */
    public function index(): void
    {
        $tripService = new TripService();
        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $result = $tripService->getAvailableTripsPage($page, self::TRIPS_PER_PAGE);

        $this->render('home/index', [
            'trips' => $result['items'],
            'pagination' => $result['pagination'],
        ]);
    }
}
