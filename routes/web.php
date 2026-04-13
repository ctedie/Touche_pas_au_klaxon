<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\TripController;

return [
    '/touche-pas-au-klaxon/public/' => [HomeController::class, 'index'],

    '/touche-pas-au-klaxon/public/login' => [
        'GET' => [AuthController::class, 'showLoginForm'],
        'POST' => [AuthController::class, 'login'],
    ],

    '/touche-pas-au-klaxon/public/logout' => [AuthController::class, 'logout'],

    '/touche-pas-au-klaxon/public/trip/show' => [TripController::class, 'show'],
];