<?php

// Prepare the database for operation, if required
//require base_path('prepare_database.php');

require __DIR__ . '/../vendor/autoload.php';

require '../helpers.php';

// spl_autoload_register(function($class) {
//     $path = base_path('Framework/' . $class . '.php');
//     if (file_exists($path)) {
//         require $path;
//     }
// });

// Initialize the router
$router = new Router();

// Define routes
require base_path('routes.php');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Route the request
$router->route($uri, $method);
