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
$router->add('dashboard/export', ['controller' => 'dashboard', 'action' => 'export']);
$router->add('columns', ['controller' => 'columns', 'action' => 'index']);
$router->add('schools', ['controller' => 'schools', 'action' => 'index']);
$router->add('settings', ['controller' => 'settings', 'action' => 'index']);
$router->add('profile', ['controller' => 'profile', 'action' => 'index']);

// Settings routes
$router->add('settings', ['controller' => 'settings', 'action' => 'index']);
$router->add('settings/importSchools', ['controller' => 'settings', 'action' => 'importSchools']);
$router->add('settings/importSchoolAdmins', ['controller' => 'settings', 'action' => 'importSchoolAdmins']);
$router->add('settings/downloadTemplate/([^/]+)', ['controller' => 'settings', 'action' => 'downloadTemplate']);
$router->add('settings/addColumn', ['controller' => 'settings', 'action' => 'addColumn']);
$router->add('settings/updateColumn', ['controller' => 'settings', 'action' => 'updateColumn']);
$router->add('settings/deleteColumn', ['controller' => 'settings', 'action' => 'deleteColumn']);
$router->add('settings/addSchool', ['controller' => 'settings', 'action' => 'addSchool']);
$router->add('settings/updateSchool', ['controller' => 'settings', 'action' => 'updateSchool']);
$router->add('settings/deleteSchool', ['controller' => 'settings', 'action' => 'deleteSchool']);
$router->add('settings/addSchoolAdmin', ['controller' => 'settings', 'action' => 'addSchoolAdmin']);
$router->add('settings/updateSchoolAdmin', ['controller' => 'settings', 'action' => 'updateSchoolAdmin']);
$router->add('settings/deleteSchoolAdmin', ['controller' => 'settings', 'action' => 'deleteSchoolAdmin']);

// API routes
$router->add('api/columns', ['controller' => 'api', 'action' => 'columns']);
$router->add('api/data', ['controller' => 'api', 'action' => 'data']);
$router->add('api/data/update', ['controller' => 'dashboard', 'action' => 'updateData']);
$router->add('api/data/bulk-update', ['controller' => 'api', 'action' => 'bulkUpdate']);
$router->add('api/export', ['controller' => 'api', 'action' => 'export']);
$router->add('api/school-admin', ['controller' => 'api', 'action' => 'createSchoolAdmin']);

// Get the URL
$url = trim($_SERVER['REQUEST_URI'], '/');

// Debug məlumatları
error_log("Requested URL: " . $url);
error_log("Session data: " . print_r($_SESSION, true));

try {
    $router->dispatch($url);
} catch (Exception $e) {
    error_log("Router error: " . $e->getMessage() . "\nTrace: " . $e->getTraceAsString());
    
    if ($_ENV['APP_DEBUG'] === 'true') {
        echo "<div class='alert alert-danger'>";
        echo "<h4>Error " . $e->getCode() . "</h4>";
        echo "<p>" . $e->getMessage() . "</p>";
        if ($e->getCode() === 404) {
            echo "<p>Requested URL: " . $url . "</p>";
        }
        echo "</div>";
    } else {
        echo "<div class='alert alert-danger'>Səhifə tapılmadı</div>";
    }
}