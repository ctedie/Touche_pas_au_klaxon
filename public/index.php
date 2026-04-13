<?php

declare(strict_types=1);

use App\Core\Session;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

Session::start();

$routes = require __DIR__ . '/../routes/web.php';

$uri = strtok($_SERVER['REQUEST_URI'], '?');
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$route = $routes[$uri] ?? null;

if (is_array($route) && isset($route[0], $route[1]) && is_string($route[0]) && is_string($route[1])) {
    $controller = $route[0];
    $action = $route[1];

    (new $controller())->$action();
    exit;
}

if (is_array($route) && isset($route[$method]) && is_array($route[$method])) {
    /** @var array{0: class-string, 1: string} $handler */
    $handler = $route[$method];
    $controller = $handler[0];
    $action = $handler[1];

    (new $controller())->$action();
    exit;
}

http_response_code(404);
echo '404';