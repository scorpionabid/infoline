<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$router = new App\Core\Router();

// Auth routes
$router->add('', ['controller' => 'auth', 'action' => 'login']);
$router->add('login', ['controller' => 'auth', 'action' => 'login']);
$router->add('logout', ['controller' => 'auth', 'action' => 'logout']);

// Dashboard routes
$router->add('dashboard', ['controller' => 'dashboard', 'action' => 'index']);
$router->add('dashboard/', ['controller' => 'dashboard', 'action' => 'index']); 
$router->add('columns', ['controller' => 'columns', 'action' => 'index']);
$router->add('schools', ['controller' => 'schools', 'action' => 'index']);
$router->add('settings', ['controller' => 'settings', 'action' => 'index']);
$router->add('profile', ['controller' => 'profile', 'action' => 'index']);

// API routes
$router->add('api/columns', ['controller' => 'api', 'action' => 'columns']);
$router->add('api/data', ['controller' => 'api', 'action' => 'data']);
$router->add('api/school-admin', ['controller' => 'api', 'action' => 'createSchoolAdmin']);
$router->add('api/export', ['controller' => 'api', 'action' => 'export']);

// Get the URL
$url = trim($_SERVER['REQUEST_URI'], '/');

// Debug məlumatları
error_log("Requested URL: " . $url);
error_log("Session data: " . print_r($_SESSION, true));

try {
    $router->dispatch($url);
} catch (Exception $e) {
    error_log("Router error: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    http_response_code($e->getCode());
    
    // Debug mode-da xəta məlumatlarını göstər
    if ($_ENV['APP_DEBUG'] === 'true') {
        echo "<h1>Error " . $e->getCode() . "</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        echo $e->getMessage();
    }
}