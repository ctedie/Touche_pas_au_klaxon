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

    $basePath . '/admin' => [AdminController::class, 'dashboard'],
    $basePath . '/admin/users' => [AdminController::class, 'users'],
    $basePath . '/admin/agencies' => [AdminController::class, 'agencies'],
    $basePath . '/admin/trips' => [AdminController::class, 'trips'],
];
