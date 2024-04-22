<?php

// Prepare the database for operation, if required
//require base_path('prepare_database.php');

require '../helpers.php';
require base_path('Router.php');
require base_path('Database.php');

// Initialize the router
$router = new Router();

// Define routes
require base_path('routes.php');

// Get current URI and HTTP method
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Route the request
$router->route($uri, $method);
