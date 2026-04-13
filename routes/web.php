<?php

declare(strict_types=1);

use App\Controllers\HomeController;
use App\Controllers\TripController;

return [
    '/' => [HomeController::class, 'index'],
    '/touche-pas-au-klaxon/public' => [HomeController::class, 'index'],
    '/touche-pas-au-klaxon/public/' => [HomeController::class, 'index'],

    '/trip/show' => [TripController::class, 'show'],
    '/touche-pas-au-klaxon/public/trip/show' => [TripController::class, 'show'],
];