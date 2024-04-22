<?php

$routes = require base_path('routes.php');

if (key_exists($uri, $routes)) {
    require base_path($routes[$uri]);
} else {
    http_response_code(404);
    require base_path($routes['404']);
}