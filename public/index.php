<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$routes = require __DIR__ . '/../routes/web.php';

$uri = strtok($_SERVER['REQUEST_URI'], '?');

if (isset($routes[$uri])) {
    $controller = $routes[$uri][0];
    $method = $routes[$uri][1];

    (new $controller())->$method();
} else {
    http_response_code(404);
    echo '404';
}