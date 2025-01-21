<?php
namespace App\Core;

class Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function view($view, $data = []) {
        // Convert dots to directory separators
        $view = str_replace('.', '/', $view);
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            error_log("View file not found: $viewFile");
            throw new \Exception("View '$view' not found");
        }
        
        // Make flash messages available to all views
        if (isset($_SESSION['flash_message'])) {
            $data['flash_message'] = $_SESSION['flash_message'];
            $data['flash_type'] = $_SESSION['flash_type'] ?? 'info';
            unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        }
        
        // Extract variables for the view
        if (!empty($data)) {
            extract($data);
        }
        
        // Start output buffering
        ob_start();
        require_once $viewFile;
        return ob_get_clean();
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }
}