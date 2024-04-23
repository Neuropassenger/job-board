<?php

namespace Framework;

use App\Controllers\ErrorController;

class Router {
    protected $routes = [];

    /**
     * Add a new route
     *
     * @param string $method
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function register_route($method, $uri, $action) {
        list($controller, $controller_method) = explode('@', $action);
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controller_method' => $controller_method
        ];
    }

    /**
     * Add a GET route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function get($uri, $controller) {
        $this->register_route('GET', $uri, $controller);
    }

    /**
     * Add a POST route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function post($uri, $controller) {
        $this->register_route('POST', $uri, $controller);
    }

    /**
     * Add a PUT route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function put($uri, $controller) {
        $this->register_route('PUT', $uri, $controller);
    }

    /**
     * Add a DELETE route
     * 
     * @param string $uri
     * @param string $controller
     * @return void
     */
    public function delete($uri, $controller) {
        $this->register_route('DELETE', $uri, $controller);
    }

    /**
     * Route the request
     * 
     * @param string $uri
     * @param string $method
     * @return void
     */
    public function route($uri, $method) {
        foreach($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                // Extract controller and controller method
                $controller = 'App\\Controllers\\' . $route['controller'];
                $controller_method = $route['controller_method'];

                // Instantiate the controller and call the method
                $controller_instance = new $controller();
                $controller_instance->$controller_method();
                return;
            }
        }

        ErrorController::not_found();
    }
}