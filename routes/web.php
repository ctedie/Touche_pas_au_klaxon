<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\TripController;

return [
    '/' => [HomeController::class, 'index'],

    '/login' => [AuthController::class, 'showLoginForm'],
    '/login/submit' => [AuthController::class, 'login'],
    '/logout' => [AuthController::class, 'logout'],

    '/trip/show' => [TripController::class, 'show'],
    '/trip/create' => [TripController::class, 'create'],
    '/trip/store' => [TripController::class, 'store'],
];