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
        $this->columnModel = new Column();
        $this->dataModel = new DataValue();
        $this->schoolModel = new School();
        $this->userModel = new User();
    }

    private function getJsonInput() {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function columns() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($_SESSION['role'] !== 'superadmin') {
                $this->json(['error' => 'Unauthorized']);
                return;
            }

            $data = $this->getJsonInput();
            if (!isset($data['name']) || !isset($data['type'])) {
                $this->json(['error' => 'Invalid data']);
                return;
            }

            $columnData = [
                'name' => $data['name'],
                'type' => $data['type'],
                'deadline' => $data['deadline'] ?? null,
                'is_active' => $data['is_active'] ?? true
            ];

            $columnId = $this->columnModel->create($columnData);
            if ($columnId) {
                $this->notifyNewColumn($data['name']);
                $this->json(['success' => true, 'id' => $columnId]);
            } else {
                $this->json(['error' => 'Failed to create column']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
            if ($_SESSION['role'] !== 'superadmin') {
                $this->json(['error' => 'Unauthorized']);
                return;
            }

            $id = $_GET['id'] ?? null;
            if (!$id) {
                $this->json(['error' => 'ID is required']);
                return;
            }

            if ($this->columnModel->delete($id)) {
                $this->json(['success' => true]);
            } else {
                $this->json(['error' => 'Failed to delete column']);
            }
        } else {
            $this->json(['error' => 'Method not allowed']);
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

    public function export() {
        if ($_SESSION['role'] !== 'superadmin') {
            $this->json(['error' => 'Unauthorized']);
            return;
        }

        $columns = $this->columnModel->findAll();
        $schools = $this->schoolModel->findAll();
        $data = [];

        foreach ($schools as $school) {
            $row = ['Məktəb' => $school['name']];
            foreach ($columns as $column) {
                $value = $this->dataModel->findBySchoolAndColumn($school['id'], $column['id']);
                $row[$column['name']] = $value ? $value['value'] : '';
            }
            $data[] = $row;
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="export.csv"');
        
        $f = fopen('php://output', 'w');
        fputcsv($f, array_keys($data[0]));
        foreach ($data as $row) {
            fputcsv($f, $row);
        }
        fclose($f);
    }

    private function notifyNewColumn($columnName) {
        // WebSocket notification implementation
    }

    private function notifyDataUpdate($schoolId, $columnId, $value) {
        // WebSocket notification implementation
    }
}