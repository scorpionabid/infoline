<?php
namespace App\Core;

class Router {
    private $routes = [];
    private $params = [];

    public function add($route, $params = []) {
        // Convert the route to a regular expression
        $route = preg_replace('/\//', '\\/', $route);
        
        // Convert variables e.g. {id}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        
        // Convert variables with custom patterns e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        
        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';
        
        error_log("Adding route: " . $route . " with params: " . print_r($params, true));
        $this->routes[$route] = $params;
    }

    public function match($url) {
        error_log("\n\n=== Router Match ===");
        error_log("Matching URL: " . $url);
        error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
        
        foreach ($this->routes as $route => $params) {
            error_log("\nTrying route: " . $route);
            error_log("Route params: " . print_r($params, true));
            
            // Method yoxlaması
            if (isset($params['method']) && $params['method'] !== $_SERVER['REQUEST_METHOD']) {
                error_log("Method does not match. Expected: " . $params['method'] . ", Got: " . $_SERVER['REQUEST_METHOD']);
                continue;
            }
            
            if (preg_match($route, $url, $matches)) {
                error_log("Route matched!");
                
                // Add named parameters to the parameters array
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        if ($key === 'param') {
                            $params['parameters'] = [$match];
                        } else {
                            $params[$key] = $match;
                        }
                    }
                }
                
                $this->params = $params;
                return true;
            }
        }
        
        error_log("No matching route found");
        return false;
    }

    public function dispatch($url) {
        error_log("\n=== Router Dispatch ===");
        error_log("Dispatching URL: " . $url);
        
        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = "App\\Controllers\\{$controller}Controller";

            error_log("Controller: " . $controller);

            if (!class_exists($controller)) {
                error_log("Controller not found: " . $controller);
                throw new \Exception("Controller $controller not found", 404);
            }

            $controller_object = new $controller();
            $action = $this->params['action'];
            $action = $this->convertToCamelCase($action);

            error_log("Action: " . $action);
            if (isset($this->params['parameters'])) {
                error_log("Parameters: " . print_r($this->params['parameters'], true));
            }

            if (!method_exists($controller_object, $action)) {
                error_log("Action not found: " . $controller . "::" . $action);
                throw new \Exception("Method $action not found in $controller", 404);
            }

            // Call the action with parameters if they exist
            if (isset($this->params['parameters'])) {
                error_log("Calling $action with parameters: " . print_r($this->params['parameters'], true));
                $result = call_user_func_array([$controller_object, $action], $this->params['parameters']);
            } else {
                error_log("Calling $action without parameters");
                $result = $controller_object->$action();
            }

            if ($result !== null) {
                echo $result;
            }
            return;
        }
        
        error_log("No route matched in dispatch!");
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

    public function __construct() {
        error_log("Initializing Router");
        
        // Categories API routes
        $this->add('api/categories', [
            'controller' => 'Category',
            'action' => 'index',
            'method' => 'GET'
        ]);

        $this->add('api/categories', [
            'controller' => 'Category',
            'action' => 'create',
            'method' => 'POST'
        ]);

        $this->add('api/categories/{id:\d+}', [
            'controller' => 'Category',
            'action' => 'delete',
            'method' => 'DELETE',
            'parameters' => []
        ]);

        // Settings API routes
        $this->add('settings/deleteColumn', [
            'controller' => 'Settings',
            'action' => 'deleteColumn',
            'method' => 'POST'
        ]);

        $this->add('settings/getColumn', [
            'controller' => 'Settings',
            'action' => 'getColumn',
            'method' => 'GET'
        ]);

        $this->add('settings/updateColumn', [
            'controller' => 'Settings',
            'action' => 'updateColumn',
            'method' => 'POST'
        ]);

        // Yeni route-lar
        $this->add('api/data', [
            'controller' => 'dashboard',
            'action' => 'getData',
            'method' => 'GET'
        ]);

        $this->add('export/excel', [
            'controller' => 'dashboard',
            'action' => 'exportExcel',
            'method' => 'GET'
        ]);

        $this->add('export/excel/{id:\d+}', [
            'controller' => 'dashboard',
            'action' => 'exportExcel',
            'method' => 'GET'
        ]);

        error_log("Routes initialized: " . print_r($this->routes, true));
    }
}