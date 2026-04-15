<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$routes = require __DIR__ . '/../routes/web.php';

$uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
$uri = rtrim($uri, '/');

if ($uri === '') {
    $uri = '/';
}

if (isset($routes[$uri])) {
    $controller = $routes[$uri][0];
    $method = $routes[$uri][1];

    (new $controller())->$method();
    exit;
}

http_response_code(404);
echo '404';