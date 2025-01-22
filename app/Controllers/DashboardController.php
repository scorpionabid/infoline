<?php
namespace App\Controllers;

use App\Models\School;
use App\Models\Column;
use App\Models\Data;
use App\Core\View;
use App\Core\Auth;

class DashboardController {
    private $schoolModel;
    private $columnModel;
    private $dataModel;
    private $auth;

    public function __construct() {
        $this->schoolModel = new School();
        $this->columnModel = new Column();
        $this->dataModel = new Data();
        $this->auth = new Auth();
    }

    public function index() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Disable caching
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");

        // Check if user is logged in
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            header('Location: /login');
            exit;
        }

        try {
            if ($_SESSION['role'] === 'super_admin') {
                $schools = $this->schoolModel->getAll();
                $columns = $this->columnModel->getAll();
                $data = $this->dataModel->getAllSchoolData();
                
                return View::render('dashboard/superadmin', [
                    'schools' => $schools,
                    'columns' => $columns,
                    'data' => $data
                ]);
            } else {
                // School admin only sees their own school
                $school = $this->schoolModel->getById($_SESSION['school_id']);
                if (!$school) {
                    throw new \Exception('Məktəb tapılmadı');
                }

                $columns = $this->columnModel->getAll();
                $data = $this->dataModel->getSchoolData($school['id']);
                
                return View::render('dashboard/school', [
                    'school' => $school,
                    'columns' => $columns,
                    'data' => $data
                ]);
            }
        } catch (\Exception $e) {
            error_log("Dashboard Error: " . $e->getMessage());
            return View::render('dashboard/superadmin', ['error' => true]);
        }
    }

    public function export() {
        // TODO: Excel export functionality
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="export.xls"');
        header('Cache-Control: max-age=0');
        
        // Get data
        $schools = $this->schoolModel->getAll();
        $columns = $this->columnModel->getAll();
        $data = $this->dataModel->getAllSchoolData();
        
        // Create table
        echo '<table border="1">';
        
        // Headers
        echo '<tr><th>Məktəb</th>';
        foreach ($columns as $column) {
            echo '<th>' . htmlspecialchars($column['name']) . '</th>';
        }
        echo '</tr>';
        
        // Data
        foreach ($schools as $school) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($school['name']) . '</td>';
            
            foreach ($columns as $column) {
                $value = '';
                foreach ($data as $item) {
                    if ($item['school_id'] == $school['id'] && 
                        $item['column_id'] == $column['id']) {
                        $value = $item['value'];
                        break;
                    }
                }
                echo '<td>' . htmlspecialchars($value) . '</td>';
            }
            echo '</tr>';
        }
        
        echo '</table>';
        exit;
    }

    public function updateData() {
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        // Get POST data
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data || !isset($data['school_id']) || !isset($data['column_id']) || !isset($data['value'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Invalid data']);
            return;
        }

        // Check permissions
        if ($_SESSION['role'] === 'school_admin' && $_SESSION['school_id'] != $data['school_id']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'error' => 'Unauthorized']);
            return;
        }

        try {
            $result = $this->dataModel->update($data['school_id'], $data['column_id'], $data['value']);
            echo json_encode($result);
        } catch (\Exception $e) {
            error_log("Data Update Error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Server error']);
        }
    }
}