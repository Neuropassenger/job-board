<?php

session_start();

require __DIR__ . '/../vendor/autoload.php';
require '../helpers.php';

// Prepare the database for operation, if required
//require base_path('prepare_database.php');

use Framework\Router;

// Initialize the router
$router = new Router();

// Define routes
require base_path('routes.php');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Route the request
$router->route($uri);
