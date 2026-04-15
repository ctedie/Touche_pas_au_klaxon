<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\TripController;

$basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
$basePath = rtrim($basePath, '/');

return [
    $basePath === '' ? '/' : $basePath => [HomeController::class, 'index'],

    $basePath . '/login' => [AuthController::class, 'showLoginForm'],
    $basePath . '/login/submit' => [AuthController::class, 'login'],
    $basePath . '/logout' => [AuthController::class, 'logout'],

    $basePath . '/trip/create' => [TripController::class, 'create'],
    $basePath . '/trip/store' => [TripController::class, 'store'],
    $basePath . '/trip/show' => [TripController::class, 'show'],
    $basePath . '/trip/edit' => [TripController::class, 'edit'],
    $basePath . '/trip/update' => [TripController::class, 'update'],
    $basePath . '/trip/delete' => [TripController::class, 'delete'],
    $basePath . '/trip/reserve' => [TripController::class, 'reserve'],

    $basePath . '/reservations' => [TripController::class, 'reservations'],
    $basePath . '/reservations/cancel' => [TripController::class, 'cancelReservation'],

    $basePath . '/admin' => [AdminController::class, 'dashboard'],
    $basePath . '/admin/users' => [AdminController::class, 'users'],
    $basePath . '/admin/agencies' => [AdminController::class, 'agencies'],
    $basePath . '/admin/agencies/create' => [AdminController::class, 'createAgency'],
    $basePath . '/admin/agencies/store' => [AdminController::class, 'storeAgency'],
    $basePath . '/admin/agencies/edit' => [AdminController::class, 'editAgency'],
    $basePath . '/admin/agencies/update' => [AdminController::class, 'updateAgency'],
    $basePath . '/admin/agencies/delete' => [AdminController::class, 'deleteAgency'],
    $basePath . '/admin/trips' => [AdminController::class, 'trips'],
    $basePath . '/admin/trips/delete' => [AdminController::class, 'deleteTrip'],
];
