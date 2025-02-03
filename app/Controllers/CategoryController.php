<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;

class CategoryController extends Controller {
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->categoryModel = new Category();
    }

    private function ensureDefaultCategory() {
        try {
            $category = new Category();
            $defaultExists = $category->findByName('Digər');
            
            if (!$defaultExists) {
                $category->create([
                    'name' => 'Digər',
                    'description' => 'Default kateqoriya',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'is_default' => 1
                ]);
            }
        } catch (\Exception $e) {
            error_log("Error ensuring default category: " . $e->getMessage());
        }
    }

    // GET /api/categories
    public function index() {
        try {
            $this->ensureDefaultCategory();
            $categories = $this->categoryModel->getAll();
            return $this->jsonResponse([
                'success' => true,
                'data' => $categories,
                'message' => 'Kateqoriyalar uğurla yükləndi'
            ]);
        } catch (\Exception $e) {
            error_log("Error in CategoryController index: " . $e->getMessage());
            return $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // POST /api/categories
    public function create() {
        try {
            error_log("Create method called with raw input: " . file_get_contents('php://input'));
            
            // JSON data-nı alırıq
            $data = json_decode(file_get_contents('php://input'), true);
            error_log("Decoded JSON data: " . print_r($data, true));

            if (!isset($data['name']) || empty(trim($data['name']))) {
                throw new \Exception('Kateqoriya adı tələb olunur');
            }

            $categoryData = [
                'name' => trim($data['name']),
                'description' => isset($data['description']) ? trim($data['description']) : null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            error_log("Prepared category data: " . print_r($categoryData, true));

            try {
                $result = $this->categoryModel->create($categoryData);
                error_log("Create result: " . print_r($result, true));
                
                if ($result) {
                    $categories = $this->categoryModel->getAll();
                    $this->jsonResponse([
                        'success' => true,
                        'data' => $categories,
                        'message' => 'Kateqoriya uğurla əlavə edildi'
                    ]);
                } else {
                    throw new \Exception('Kateqoriya əlavə edilərkən xəta baş verdi');
                }
            } catch (\Exception $e) {
                error_log("Database error: " . $e->getMessage());
                throw new \Exception('Database xətası: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            error_log("Error in create method: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // DELETE /api/categories/{id}
    public function delete() {
        try {
            // URL-dən ID-ni əldə edirik
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            preg_match('/categories\/(\d+)$/', $path, $matches);
            
            if (!isset($matches[1])) {
                throw new \Exception('Kateqoriya ID tapılmadı');
            }

            $id = (int)$matches[1];
            error_log("Deleting category with ID: " . $id);

            if (!$id) {
                throw new \Exception('Kateqoriya ID tələb olunur');
            }

            // Default kateqoriyanı yoxlayırıq
            $category = $this->categoryModel->getById($id);
            if (!$category) {
                throw new \Exception('Kateqoriya tapılmadı');
            }

            if ($category['is_default'] == 1) {
                throw new \Exception('Default kateqoriyanı silmək olmaz');
            }

            $result = $this->categoryModel->delete($id);
            
            if ($result) {
                // Yenilənmiş kateqoriya siyahısını qaytarırıq
                $categories = $this->categoryModel->getAll();
                return $this->jsonResponse([
                    'success' => true,
                    'data' => $categories,
                    'message' => 'Kateqoriya uğurla silindi'
                ]);
            } else {
                throw new \Exception('Kateqoriya silinərkən xəta baş verdi');
            }
        } catch (\Exception $e) {
            error_log("Error in delete: " . $e->getMessage());
            return $this->jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function jsonResponse($data, $statusCode = 200) {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}