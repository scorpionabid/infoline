<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\School;
use App\Models\Column;
use App\Models\DataValue;

class DashboardController extends Controller {
    private $schoolModel;
    private $columnModel;
    private $dataModel;

    public function __construct() {
        parent::__construct(); // Parent constructor çağır
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        error_log("DashboardController constructed");
        error_log("Session data in DashboardController: " . print_r($_SESSION, true));
        
        $this->schoolModel = new School();
        $this->columnModel = new Column();
        $this->dataModel = new DataValue();
    }

    public function index() {
        error_log("DashboardController::index called");
        
        try {
            // Session yoxlaması
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
                error_log("No session data found, redirecting to login");
                $_SESSION['flash_message'] = 'Zəhmət olmasa daxil olun';
                $_SESSION['flash_type'] = 'warning';
                header('Location: /login');
                exit;
            }

            error_log("User role: " . $_SESSION['role']);

            if ($_SESSION['role'] === 'superadmin') {
                try {
                    error_log("Loading superadmin dashboard");
                    $schools = $this->schoolModel->findAll();
                    $columns = $this->columnModel->findAll();
                    $data = $this->dataModel->getAllSchoolData();
                    
                    error_log("Loaded data: " . print_r([
                        'schools_count' => count($schools),
                        'columns_count' => count($columns),
                        'data_count' => count($data)
                    ], true));
                    
                    return $this->view('dashboard/superadmin', [
                        'schools' => $schools,
                        'columns' => $columns,
                        'data' => $data
                    ]);
                } catch (\Exception $e) {
                    error_log("Error in superadmin dashboard: " . $e->getMessage());
                    $_SESSION['flash_message'] = 'Məlumatları yükləyərkən xəta baş verdi';
                    $_SESSION['flash_type'] = 'danger';
                    return $this->view('dashboard/superadmin', ['error' => true]);
                }
            } elseif ($_SESSION['role'] === 'school_admin') {
                try {
                    error_log("Loading school admin dashboard");
                    $school = $this->schoolModel->findByAdminId($_SESSION['user_id']);
                    if (!$school) {
                        throw new \Exception('Məktəb tapılmadı');
                    }

                    $columns = $this->columnModel->findAll();
                    $data = $this->dataModel->getSchoolData($school['id']);
                    
                    error_log("Loaded school data: " . print_r([
                        'school_id' => $school['id'],
                        'columns_count' => count($columns),
                        'data_count' => count($data)
                    ], true));
                    
                    return $this->view('dashboard/school', [
                        'school' => $school,
                        'columns' => $columns,
                        'data' => $data
                    ]);
                } catch (\Exception $e) {
                    error_log("Error in school admin dashboard: " . $e->getMessage());
                    $_SESSION['flash_message'] = $e->getMessage();
                    $_SESSION['flash_type'] = 'danger';
                    header('Location: /logout');
                    exit;
                }
            } else {
                error_log("Invalid role: " . $_SESSION['role']);
                throw new \Exception('İcazəsiz giriş');
            }
        } catch (\Exception $e) {
            error_log("Dashboard access error: " . $e->getMessage());
            $_SESSION['flash_message'] = 'Sistemə giriş zamanı xəta baş verdi';
            $_SESSION['flash_type'] = 'danger';
            header('Location: /login');
            exit;
        }
    }
}