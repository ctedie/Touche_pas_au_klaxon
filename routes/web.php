<?php

use App\Controllers\HomeController;

return [
    '/' => [HomeController::class, 'index'],
    '/touche-pas-au-klaxon/public' => [HomeController::class, 'index'],
    '/touche-pas-au-klaxon/public/' => [HomeController::class, 'index'],
];