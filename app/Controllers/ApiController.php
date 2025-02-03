<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Column;
use App\Models\DataValue;
use App\Models\School;
use App\Models\User;

class ApiController extends Controller {
    private $columnModel;
    private $dataModel;
    private $schoolModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->columnModel = new Column();
        $this->dataModel = new DataValue();
        $this->schoolModel = new School();
        $this->userModel = new User();
    }

    private function getJsonInput() {
        $jsonData = file_get_contents('php://input');
        error_log("Raw JSON input: " . $jsonData);
        $data = json_decode($jsonData, true);
        error_log("Decoded JSON: " . print_r($data, true));
        return $data;
    }

    public function categories($id = null) {
        error_log("Categories endpoint called with method: " . $_SERVER['REQUEST_METHOD']);
        error_log("User role: " . $_SESSION['role']);

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $result = $this->columnModel->getCategories();
            error_log("GET categories result: " . print_r($result, true));
            
            if ($result['success']) {
                $this->json(['success' => true, 'data' => $result['data']]);
            } else {
                $this->json(['success' => false, 'message' => $result['message']]);
            }
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_SESSION['role'] !== 'super_admin') {
                error_log("Permission denied for role: " . $_SESSION['role']);
                $this->json(['success' => false, 'message' => 'İcazə yoxdur']);
                return;
            }

            // Get JSON data
            $data = $this->getJsonInput();
            
            // If JSON data is empty, try POST data
            if (empty($data)) {
                $data = $_POST;
                error_log("Using POST data: " . print_r($data, true));
            }

            if (empty($data['name'])) {
                error_log("Category name is empty");
                $this->json(['success' => false, 'message' => 'Kateqoriya adı tələb olunur']);
                return;
            }

            $result = $this->columnModel->createCategory([
                'name' => $data['name'],
                'description' => $data['description'] ?? null
            ]);

            error_log("Create category result: " . ($result ? "true" : "false"));

            if ($result) {
                // Yeni kateqoriyaları qaytarırıq
                $categories = $this->columnModel->getCategories();
                error_log("New categories list: " . print_r($categories, true));
                
                $this->json([
                    'success' => true, 
                    'message' => 'Kateqoriya əlavə edildi',
                    'data' => $categories['data']
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Xəta baş verdi']);
            }
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && $id) {
            if ($_SESSION['role'] !== 'super_admin') {
                error_log("Permission denied for role: " . $_SESSION['role']);
                $this->json(['success' => false, 'message' => 'İcazə yoxdur']);
                return;
            }

            error_log("Deleting category with ID: " . $id);
            $result = $this->columnModel->deleteCategory($id);
            error_log("Delete category result: " . ($result ? "true" : "false"));

            if ($result) {
                // Yeni kateqoriyaları qaytarırıq
                $categories = $this->columnModel->getCategories();
                error_log("New categories list after delete: " . print_r($categories, true));
                
                $this->json([
                    'success' => true, 
                    'message' => 'Kateqoriya silindi',
                    'data' => $categories['data']
                ]);
            } else {
                $this->json(['success' => false, 'message' => 'Xəta baş verdi']);
            }
            return;
        }
    }

    public function columns() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $columns = $this->columnModel->getAll();
            $this->json($columns);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_SESSION['role'] !== 'superadmin') {
                $this->json(['success' => false, 'message' => 'İcazə yoxdur']);
                return;
            }

            $data = $this->getJsonInput();
            if (!isset($data['name']) || !isset($data['type'])) {
                $this->json(['success' => false, 'message' => 'Məlumatlar tam deyil']);
                return;
            }

            $columnData = [
                'name' => $data['name'],
                'type' => $data['type'],
                'deadline' => $data['deadline'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'category_id' => $data['category_id'] ?? null
            ];

            $columnId = $this->columnModel->create($columnData);
            if ($columnId) {
                $this->notifyNewColumn($data['name']);
                $this->json(['success' => true, 'id' => $columnId]);
            } else {
                $this->json(['success' => false, 'message' => 'Xəta baş verdi']);
            }
            return;
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if ($_SESSION['role'] !== 'superadmin') {
                $this->json(['success' => false, 'message' => 'İcazə yoxdur']);
                return;
            }

            $id = $_GET['id'] ?? null;
            if (!$id) {
                $this->json(['success' => false, 'message' => 'ID tələb olunur']);
                return;
            }

            if ($this->columnModel->delete($id)) {
                $this->json(['success' => true]);
            } else {
                $this->json(['success' => false, 'message' => 'Xəta baş verdi']);
            }
        } else {
            $this->json(['success' => false, 'message' => 'Metod icazə verilmir']);
        }
    }

    public function data() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed']);
            return;
        }

        $data = $this->getJsonInput();
        if (!isset($data['column_id']) || !isset($data['value'])) {
            $this->json(['error' => 'Invalid data']);
            return;
        }

        if ($_SESSION['role'] === 'school_admin') {
            $school = $this->schoolModel->findByAdminId($_SESSION['user_id']);
            if (!$school) {
                $this->json(['error' => 'School not found']);
                return;
            }
            $schoolId = $school['id'];
        } else {
            $schoolId = $data['school_id'] ?? null;
            if (!$schoolId) {
                $this->json(['error' => 'School ID required']);
                return;
            }
        }

        $existingData = $this->dataModel->findBySchoolAndColumn($schoolId, $data['column_id']);
        
        $dataToSave = [
            'school_id' => $schoolId,
            'column_id' => $data['column_id'],
            'value' => $data['value']
        ];

        if ($existingData) {
            $success = $this->dataModel->update($existingData['id'], $dataToSave);
        } else {
            $success = $this->dataModel->create($dataToSave);
        }

        if ($success) {
            $this->notifyDataUpdate($schoolId, $data['column_id'], $data['value']);
            $this->json(['success' => true]);
        } else {
            $this->json(['error' => 'Failed to save data']);
        }
    }

    public function createSchoolAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['error' => 'Method not allowed']);
            return;
        }

        if ($_SESSION['role'] !== 'superadmin') {
            $this->json(['error' => 'Unauthorized']);
            return;
        }

        $data = $this->getJsonInput();
        if (!isset($data['school_id']) || !isset($data['username']) || !isset($data['password'])) {
            $this->json(['error' => 'Invalid data']);
            return;
        }

        $userData = [
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role' => 'school_admin',
            'is_active' => $data['is_active'] ?? true
        ];

        $userId = $this->userModel->create($userData);
        if ($userId) {
            $schoolData = [
                'admin_id' => $userId,
                'is_active' => $data['is_active'] ?? true
            ];
            
            if ($this->schoolModel->update($data['school_id'], $schoolData)) {
                $this->json(['success' => true]);
            } else {
                $this->userModel->delete($userId);
                $this->json(['error' => 'Failed to update school']);
            }
        } else {
            $this->json(['error' => 'Failed to create user']);
        }
    }

    public function bulkUpdate() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['changes']) || !is_array($input['changes'])) {
            echo json_encode(['error' => 'Invalid data']);
            return;
        }

        $success = true;
        $errors = [];

        foreach ($input['changes'] as $change) {
            if (!isset($change['school_id'], $change['column_id'], $change['value'])) {
                $errors[] = 'Invalid change data';
                continue;
            }

            $result = $this->dataModel->updateOrCreate([
                'school_id' => $change['school_id'],
                'column_id' => $change['column_id'],
                'value' => $change['value']
            ]);

            if (!$result['success']) {
                $success = false;
                $errors[] = $result['error'];
            }
        }

        if ($success) {
            // Get updated data
            $schools = $this->schoolModel->getAll();
            $columns = $this->columnModel->getAll();
            $data = $this->dataModel->getAllSchoolData();

            echo json_encode([
                'success' => true,
                'schools' => $schools,
                'columns' => $columns,
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'error' => 'Some updates failed',
                'details' => $errors
            ]);
        }
    }

    public function export() {
        if (!isset($_SESSION['user_id'])) {
            header('HTTP/1.1 401 Unauthorized');
            exit('Unauthorized');
        }

        // Get all data
        $schools = $this->schoolModel->getAll();
        $columns = $this->columnModel->getAll();
        $data = $this->dataModel->getAllSchoolData();

        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="infoline_data.xls"');
        header('Cache-Control: max-age=0');

        // Create file pointer to php://output
        $fp = fopen('php://output', 'w');

        // Add UTF-8 BOM for proper encoding
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        // Write headers
        $headers = ['Məktəb'];
        foreach ($columns as $column) {
            $headers[] = $column['name'];
        }
        fputcsv($fp, $headers, "\t");

        // Write data
        foreach ($schools as $school) {
            $row = [$school['name']];
            foreach ($columns as $column) {
                $value = '';
                foreach ($data as $item) {
                    if ($item['school_id'] == $school['id'] && 
                        $item['column_id'] == $column['id']) {
                        $value = $item['value'];
                        break;
                    }
                }
                $row[] = $value;
            }
            fputcsv($fp, $row, "\t");
        }

        fclose($fp);
        exit;
    }

    private function notifyNewColumn($columnName) {
        // WebSocket notification implementation
    }

    private function notifyDataUpdate($schoolId, $columnId, $value) {
        // WebSocket notification implementation
    }
}