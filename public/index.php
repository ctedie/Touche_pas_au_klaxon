<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$routes = require __DIR__ . '/../routes/web.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = '/touche-pas-au-klaxon/public';

if (!is_string($uri)) {
    http_response_code(400);
    echo '400';
    exit;
}

if (str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

if ($uri === '') {
    $uri = '/';
}

if ($uri !== '/' && str_ends_with($uri, '/')) {
    $uri = rtrim($uri, '/');
}

if (isset($routes[$uri])) {
    $controller = $routes[$uri][0];
    $method = $routes[$uri][1];

    (new $controller())->$method();
    exit;
}

http_response_code(404);
echo '404';