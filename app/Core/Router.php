<?php
namespace App\Core;

class Router {
    private $routes = [];
    private $params = [];

    public function add($route, $params = []) {
        // Convert the route to a regular expression
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        $route = '/^' . $route . '$/i';

        error_log("Adding route: " . $route . " with params: " . print_r($params, true));
        $this->routes[$route] = $params;
    }

    public function match($url) {
        error_log("Matching URL: " . $url);
        error_log("Available routes: " . print_r($this->routes, true));

        foreach ($this->routes as $route => $params) {
            error_log("Checking route: " . $route);
            if (preg_match($route, $url, $matches)) {
                error_log("Route matched: " . $route);
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        
        error_log("No route matched for URL: " . $url);
        return false;
    }

    public function dispatch($url) {
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = "App\\Controllers\\{$controller}Controller";

            error_log("Dispatching to controller: " . $controller);

            if (!class_exists($controller)) {
                error_log("Controller not found: " . $controller);
                throw new \Exception('Page not found', 404);
            }

            $controller_object = new $controller();
            $action = $this->params['action'];
            $action = $this->convertToCamelCase($action);

            error_log("Calling action: " . $action);

            if (!method_exists($controller_object, $action)) {
                error_log("Action not found: " . $controller . "::" . $action);
                throw new \Exception('Page not found', 404);
            }

            $result = $controller_object->$action();
            if ($result !== null) {
                echo $result;
            }
            return;
        }
        
        error_log("No route matched for URL: " . $url);
        throw new \Exception('Page not found', 404);
    }

    private function convertToStudlyCaps($string) {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }

    private function convertToCamelCase($string) {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function getParams() {
        return $this->params;
    }
}