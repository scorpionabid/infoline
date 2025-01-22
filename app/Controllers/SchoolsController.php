<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\School;
use App\Models\User;
use App\Core\Database;

class SchoolsController extends Controller {
    private $schoolModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->schoolModel = new School();
        $this->userModel = new User();
    }

    public function index() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
            header('Location: /login');
            exit;
        }

        // Initialize database if needed
        if (!isset($_SESSION['db_initialized'])) {
            Database::getInstance()->initializeTables();
            $_SESSION['db_initialized'] = true;
        }

        $schools = $this->schoolModel->getAll();
        
        // Get admin count for each school
        foreach ($schools as &$school) {
            $admins = $this->userModel->findBySchool($school['id']);
            $school['admin_count'] = count($admins);
        }

        // View-nu render edib çap edək
        $this->view('schools/index', [
            'schools' => $schools
        ]);
    }
}