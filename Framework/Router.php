<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router {
    protected $routes = [];

    /**
     * Add a new route
     *
     * @param string $method
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * 
     * @return void
     */
    public function register_route($method, $uri, $action, $middleware = []) {
        list($controller, $controller_method) = explode('@', $action);
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controller_method' => $controller_method,
            'middleware' => $middleware
        ];
    }

    /**
     * Add a GET route
     * 
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * 
     * @return void
     */
    public function get($uri, $controller, $middleware = []) {
        $this->register_route('GET', $uri, $controller, $middleware);
    }

    /**
     * Add a POST route
     * 
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * 
     * @return void
     */
    public function post($uri, $controller, $middleware = []) {
        $this->register_route('POST', $uri, $controller, $middleware);
    }

    /**
     * Add a PUT route
     * 
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * 
     * @return void
     */
    public function put($uri, $controller, $middleware = []) {
        $this->register_route('PUT', $uri, $controller, $middleware);
    }

    /**
     * Add a DELETE route
     * 
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * 
     * @return void
     */
    public function delete($uri, $controller, $middleware = []) {
        $this->register_route('DELETE', $uri, $controller, $middleware);
    }

    /**
     * Route the request
     * 
     * @param string $uri
     * @param string $method
     * 
     * @return void
     */
    public function route($uri) {
        $request_method = $_SERVER['REQUEST_METHOD'];

        // Check for _method input
        if ($request_method === 'POST' && isset($_POST['_method'])) {
            // Override the request method with the value of _method
            $request_method = $_POST['_method'];
        }

        foreach($this->routes as $route) {

            // Split the current URI into segments
            $uri_segments = explode('/', trim($uri, '/'));
            
            // Split the route URI into segments
            $route_segments = explode('/', trim($route['uri'], '/'));
            
            $match = true;

            // Check if the number of segments matches
            if (count($uri_segments) === count($route_segments) && strtoupper($route['method']) === $request_method) {
                $params = [];

                $match = true;

                for($i = 0; $i < count($uri_segments); $i++) {
                    // If URI's do not match there is no param
                    if ($route_segments[$i] !== $uri_segments[$i] && !preg_match('/\{(.+?)}/', $route_segments[$i])) {
                        $match = false;
                        break;
                    }

                    // Check for the param and to $params array
                    if (preg_match('/\{(.+?)}/', $route_segments[$i], $matches)) {
                        $params[$matches[1]] = $uri_segments[$i];
                    }
                }

                if ($match) {
                    foreach ($route['middleware'] as $middleware) {
                        (new Authorize())->handle($middleware);
                    }

                    // Extract controller and controller method
                    $controller = 'App\\Controllers\\' . $route['controller'];
                    $controller_method = $route['controller_method'];

                    // Instantiate the controller and call the method
                    $controller_instance = new $controller();
                    $controller_instance->$controller_method($params);
                    return;
                }
            }
        }

        ErrorController::not_found();
    }
}