<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Column;
use App\Models\School;
use App\Models\User;
use App\Core\View;

class SettingsController extends Controller {
    private $columnModel;
    private $schoolModel;
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->columnModel = new Column();
        $this->schoolModel = new School();
        $this->userModel = new User();

        // Yalnız superadmin girə bilər
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        $columns = $this->columnModel->getAll();
        $schools = $this->schoolModel->getAll();
        $schoolAdmins = $this->userModel->getAllSchoolAdmins();

        return View::render('settings/index', [
            'columns' => $columns,
            'schools' => $schools,
            'schoolAdmins' => $schoolAdmins
        ]);
    }

    // Sütun əməliyyatları
    public function addColumn() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $deadline = filter_input(INPUT_POST, 'deadline', FILTER_SANITIZE_STRING);
        $isActive = filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_BOOLEAN);

        $result = $this->columnModel->create([
            'name' => $name,
            'type' => $type,
            'deadline' => $deadline,
            'is_active' => $isActive
        ]);

        return $this->json($result);
    }

    public function updateColumn() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $deadline = filter_input(INPUT_POST, 'deadline', FILTER_SANITIZE_STRING);
        $isActive = filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_BOOLEAN);

        $result = $this->columnModel->update($id, [
            'name' => $name,
            'type' => $type,
            'deadline' => $deadline,
            'is_active' => $isActive
        ]);

        return $this->json($result);
    }

    public function deleteColumn() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $result = $this->columnModel->delete($id);

        return $this->json($result);
    }

    // Məktəb əməliyyatları
    public function addSchool() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $result = $this->schoolModel->create(['name' => $name]);

        return $this->json($result);
    }

    public function updateSchool() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $result = $this->schoolModel->update($id, ['name' => $name]);

        return $this->json($result);
    }

    public function deleteSchool() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $result = $this->schoolModel->delete($id);

        return $this->json($result);
    }

    // Məktəb admin əməliyyatları
    public function addSchoolAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $schoolId = filter_input(INPUT_POST, 'school_id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

        $result = $this->userModel->createSchoolAdmin([
            'school_id' => $schoolId,
            'name' => $name,
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => 'school_admin',
            'is_active' => true
        ]);

        return $this->json($result);
    }

    public function updateSchoolAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $schoolId = filter_input(INPUT_POST, 'school_id', FILTER_VALIDATE_INT);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
        $isActive = filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_BOOLEAN);

        $data = [
            'school_id' => $schoolId,
            'name' => $name,
            'username' => $username,
            'is_active' => $isActive
        ];

        // Əgər yeni parol daxil edilibsə
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $result = $this->userModel->update($id, $data);

        return $this->json($result);
    }

    public function deleteSchoolAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $result = $this->userModel->delete($id);

        return $this->json($result);
    }
}