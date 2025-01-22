<?php
namespace App\Core;

class View {
    public static function render($view, $data = []) {
        // Extract data to make variables available in view
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewFile = __DIR__ . '/../Views/' . str_replace('.', '/', $view) . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            throw new \Exception("View file not found: {$viewFile}");
        }
        
        // Get the contents and clean the buffer
        $content = ob_get_clean();
        
        return $content;
    }
}