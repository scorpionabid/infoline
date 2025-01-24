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
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->json(['error' => 'Invalid request method']);
            }

            // Məlumatları yoxla və təmizlə
            $data = [
                'name' => htmlspecialchars(trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING)), ENT_QUOTES, 'UTF-8'),
                'type' => htmlspecialchars(trim(filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING)), ENT_QUOTES, 'UTF-8'),
                'deadline' => $this->formatDateTime(trim(filter_input(INPUT_POST, 'deadline'))),
                'is_active' => filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_BOOLEAN) ?? true
            ];

            // Məcburi sahələri yoxla
            if (empty($data['name']) || empty($data['type'])) {
                return $this->json(['error' => 'Ad və tip sahələri məcburidir']);
            }

            $result = $this->columnModel->create($data);
            
            if ($result['success']) {
                return $this->json([
                    'success' => true,
                    'message' => 'Sütun uğurla əlavə edildi',
                    'id' => $result['id']
                ]);
            } else {
                return $this->json(['error' => $result['error']]);
            }
        } catch (\Exception $e) {
            error_log("Error in addColumn: " . $e->getMessage());
            return $this->json(['error' => 'Sistem xətası baş verdi']);
        }
    }

    public function updateColumn() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->json(['error' => 'Invalid request method']);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                return $this->json(['error' => 'Düzgün sütun ID-si daxil edilməyib']);
            }

            // Sütunu yoxla
            $column = $this->columnModel->getById($id);
            if (!$column) {
                return $this->json(['error' => 'Sütun tapılmadı']);
            }

            // Məlumatları yoxla və təmizlə
            $data = [
                'name' => htmlspecialchars(trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING)), ENT_QUOTES, 'UTF-8'),
                'type' => htmlspecialchars(trim(filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING)), ENT_QUOTES, 'UTF-8'),
                'deadline' => $this->formatDateTime(trim(filter_input(INPUT_POST, 'deadline'))),
                'is_active' => filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_BOOLEAN) ?? true
            ];

            // Məcburi sahələri yoxla
            if (empty($data['name']) || empty($data['type'])) {
                return $this->json(['error' => 'Ad və tip sahələri məcburidir']);
            }

            $result = $this->columnModel->update($id, $data);
            
            if ($result['success']) {
                return $this->json([
                    'success' => true,
                    'message' => 'Sütun uğurla yeniləndi'
                ]);
            } else {
                return $this->json(['error' => $result['error']]);
            }
        } catch (\Exception $e) {
            error_log("Error in updateColumn: " . $e->getMessage());
            return $this->json(['error' => 'Sistem xətası baş verdi']);
        }
    }

    private function formatDateTime($dateTime) {
        if (empty($dateTime)) {
            return null;
        }
        
        try {
            $date = new \DateTime($dateTime);
            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            error_log("Error formatting date: " . $e->getMessage());
            return null;
        }
    }

    public function deleteColumn() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->json(['error' => 'Invalid request method']);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                return $this->json(['error' => 'Düzgün sütun ID-si daxil edilməyib']);
            }

            // Sütunu yoxla
            $column = $this->columnModel->getById($id);
            if (!$column) {
                return $this->json(['error' => 'Sütun tapılmadı']);
            }

            $result = $this->columnModel->delete($id);
            
            if ($result['success']) {
                return $this->json([
                    'success' => true,
                    'message' => 'Sütun uğurla silindi'
                ]);
            } else {
                return $this->json(['error' => $result['error']]);
            }
        } catch (\Exception $e) {
            error_log("Error in deleteColumn: " . $e->getMessage());
            return $this->json(['error' => 'Sistem xətası baş verdi']);
        }
    }

    // Məktəb əməliyyatları
    public function addSchool() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8') : '';
        if (empty($name)) {
            return $this->json(['error' => 'Məktəb adı daxil edilməlidir']);
        }

        $result = $this->schoolModel->create(['name' => $name]);
        return $this->json($result);
    }

    public function updateSchool() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            return $this->json(['error' => 'Düzgün məktəb ID-si daxil edilməyib']);
        }

        $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8') : '';
        if (empty($name)) {
            return $this->json(['error' => 'Məktəb adı daxil edilməlidir']);
        }

        // Məktəbi yoxla
        $school = $this->schoolModel->getById($id);
        if (!$school) {
            return $this->json(['error' => 'Məktəb tapılmadı']);
        }

        $result = $this->schoolModel->update($id, ['name' => $name]);
        if ($result['success']) {
            return $this->json([
                'success' => true,
                'message' => 'Məktəb uğurla yeniləndi'
            ]);
        } else {
            return $this->json(['error' => $result['error']]);
        }
    }

    public function deleteSchool() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            error_log("Invalid school ID: " . print_r($_POST, true));
            return $this->json(['error' => 'Invalid school ID']);
        }

        // Məktəbi yoxla
        $school = $this->schoolModel->getById($id);
        error_log("School data: " . print_r($school, true));
        
        if (!$school) {
            return $this->json(['error' => 'Məktəb tapılmadı']);
        }

        // Məktəbə aid adminləri yoxla
        $admins = $this->userModel->findBySchool($id);
        error_log("School admins: " . print_r($admins, true));
        
        if (!empty($admins)) {
            return $this->json(['error' => 'Bu məktəbə aid adminlər var. Əvvəlcə onları silin.']);
        }

        $result = $this->schoolModel->delete($id);
        error_log("Delete result: " . print_r($result, true));
        
        if ($result['success']) {
            return $this->json(['success' => true]);
        } else {
            return $this->json(['error' => $result['error'] ?? 'Məktəb silinərkən xəta baş verdi']);
        }
    }

    // Məktəb admin əməliyyatları
    public function addSchoolAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $schoolId = filter_input(INPUT_POST, 'school_id', FILTER_VALIDATE_INT);
        $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
        $password = htmlspecialchars(trim($_POST['password'] ?? ''), ENT_QUOTES, 'UTF-8');

        if (!$schoolId || empty($name) || empty($username) || empty($password)) {
            return $this->json(['error' => 'Bütün sahələri doldurun']);
        }

        try {
            $result = $this->userModel->createSchoolAdmin([
                'school_id' => $schoolId,
                'name' => $name,
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'school_admin',
                'is_active' => true
            ]);

            if ($result['success']) {
                return $this->json([
                    'success' => true,
                    'message' => 'Məktəb admini uğurla əlavə edildi',
                    'id' => $result['id']
                ]);
            } else {
                return $this->json(['error' => $result['error'] ?? 'Xəta baş verdi']);
            }
        } catch (\PDOException $e) {
            // Check for duplicate username
            if ($e->getCode() == 23000) {
                return $this->json(['error' => 'Bu istifadəçi adı artıq mövcuddur']);
            }
            return $this->json(['error' => 'Server xətası baş verdi']);
        }
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
            'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            'username' => htmlspecialchars($username, ENT_QUOTES, 'UTF-8'),
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
        if (!$id) {
            error_log("Invalid admin ID: " . print_r($_POST, true));
            return $this->json(['error' => 'Invalid admin ID']);
        }

        // Admini yoxla
        $admin = $this->userModel->getById($id);
        error_log("Admin data: " . print_r($admin, true));
        
        if (!$admin) {
            return $this->json(['error' => 'Admin tapılmadı']);
        }

        // Adminin rolunu yoxla
        if ($admin['role'] !== 'school_admin') {
            error_log("Invalid role: " . $admin['role']);
            return $this->json(['error' => 'Yalnız məktəb adminləri silinə bilər']);
        }

        $result = $this->userModel->delete($id);
        error_log("Delete result: " . print_r($result, true));
        
        if ($result['success']) {
            return $this->json(['success' => true]);
        } else {
            return $this->json(['error' => $result['error'] ?? 'Admin silinərkən xəta baş verdi']);
        }
    }

    public function updateAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $schoolId = filter_input(INPUT_POST, 'school_id', FILTER_VALIDATE_INT);
        $isActive = filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_BOOLEAN);
        
        // Şifrə yalnız dəyişdirildikdə yenilənir
        $password = filter_input(INPUT_POST, 'password');
        $data = [
            'username' => htmlspecialchars($username, ENT_QUOTES, 'UTF-8'),
            'email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
            'school_id' => $schoolId,
            'is_active' => $isActive
        ];
        
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $result = $this->userModel->update($id, $data);
        return $this->json($result);
    }

    public function deleteAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['error' => 'Invalid request method']);
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            error_log("Invalid admin ID: " . print_r($_POST, true));
            return $this->json(['error' => 'Invalid admin ID']);
        }

        // Admini yoxla
        $admin = $this->userModel->getById($id);
        error_log("Admin data: " . print_r($admin, true));
        
        if (!$admin) {
            return $this->json(['error' => 'Admin tapılmadı']);
        }

        // Adminin rolunu yoxla
        if ($admin['role'] !== 'school_admin') {
            error_log("Invalid role: " . $admin['role']);
            return $this->json(['error' => 'Yalnız məktəb adminləri silinə bilər']);
        }

        $result = $this->userModel->delete($id);
        error_log("Delete result: " . print_r($result, true));
        
        if ($result['success']) {
            return $this->json(['success' => true]);
        } else {
            return $this->json(['error' => $result['error'] ?? 'Admin silinərkən xəta baş verdi']);
        }
    }

    public function importSchools() {
        try {
            if (!isset($_FILES['file'])) {
                throw new \Exception('Fayl seçilməyib');
            }

            $file = $_FILES['file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Fayl yükləmə xətası');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            $importedCount = 0;
            $errors = [];

            foreach ($rows as $row) {
                try {
                    if (empty($row[0]) || empty($row[1])) continue; // Skip empty rows

                    $schoolData = [
                        'code' => $row[0],
                        'name' => htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8'),
                        'region' => htmlspecialchars($row[2] ?? '', ENT_QUOTES, 'UTF-8'),
                        'address' => htmlspecialchars($row[3] ?? '', ENT_QUOTES, 'UTF-8'),
                        'phone' => htmlspecialchars($row[4] ?? '', ENT_QUOTES, 'UTF-8'),
                        'principal_name' => htmlspecialchars($row[5] ?? '', ENT_QUOTES, 'UTF-8'),
                        'status' => 'active'
                    ];

                    $this->schoolModel->create($schoolData);
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Sətir " . ($importedCount + 2) . ": " . $e->getMessage();
                }
            }

            echo json_encode([
                'success' => true,
                'message' => $importedCount . " məktəb əlavə edildi",
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function importSchoolAdmins() {
        try {
            if (!isset($_FILES['file'])) {
                throw new \Exception('Fayl seçilməyib');
            }

            $file = $_FILES['file'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new \Exception('Fayl yükləmə xətası');
            }

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            $importedCount = 0;
            $errors = [];

            foreach ($rows as $row) {
                try {
                    if (empty($row[0]) || empty($row[1]) || empty($row[2])) continue;

                    // Find school by code
                    $school = $this->schoolModel->findByCode($row[0]);
                    if (!$school) {
                        throw new \Exception('Məktəb tapılmadı: ' . $row[0]);
                    }

                    $userData = [
                        'name' => htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8'),
                        'email' => htmlspecialchars($row[2], ENT_QUOTES, 'UTF-8'),
                        'password' => password_hash($row[3] ?? '123456', PASSWORD_DEFAULT),
                        'role' => 'school_admin',
                        'school_id' => $school['id'],
                        'status' => 'active'
                    ];

                    $this->userModel->create($userData);
                    $importedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Sətir " . ($importedCount + 2) . ": " . $e->getMessage();
                }
            }

            echo json_encode([
                'success' => true,
                'message' => $importedCount . " məktəb admini əlavə edildi",
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    private function createSchoolTemplate() {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = [
            'A1' => 'Məktəb Kodu',
            'B1' => 'Məktəb Adı',
            'C1' => 'Region',
            'D1' => 'Ünvan',
            'E1' => 'Telefon',
            'F1' => 'Direktor'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }
        
        // Add sample data
        $sampleData = [
            ['12345', 'Nümunə Məktəb', 'Bakı', 'Nümunə küç. 123', '012-345-67-89', 'Ad Soyad'],
        ];
        
        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create templates directory if it doesn't exist
        if (!file_exists(__DIR__ . '/../../public/templates')) {
            mkdir(__DIR__ . '/../../public/templates', 0777, true);
        }
        
        // Save file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save(__DIR__ . '/../../public/templates/schools_template.xlsx');
    }
    
    private function createAdminTemplate() {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set headers
        $headers = [
            'A1' => 'Məktəb Kodu',
            'B1' => 'Admin Adı',
            'C1' => 'Email',
            'D1' => 'Şifrə (Boş buraxıla bilər, default: 123456)'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
            $sheet->getStyle($cell)->getFont()->setBold(true);
        }
        
        // Add sample data
        $sampleData = [
            ['12345', 'Admin Ad Soyad', 'admin@example.com', '123456'],
        ];
        
        $row = 2;
        foreach ($sampleData as $data) {
            $sheet->fromArray($data, null, 'A' . $row);
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Create templates directory if it doesn't exist
        if (!file_exists(__DIR__ . '/../../public/templates')) {
            mkdir(__DIR__ . '/../../public/templates', 0777, true);
        }
        
        // Save file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save(__DIR__ . '/../../public/templates/admins_template.xlsx');
    }
    
    public function downloadTemplate($type) {
        try {
            $templatePath = __DIR__ . '/../../public/templates/';
            $fileName = '';
            
            switch ($type) {
                case 'schools':
                    $fileName = 'schools_template.xlsx';
                    if (!file_exists($templatePath . $fileName)) {
                        $this->createSchoolTemplate();
                    }
                    break;
                    
                case 'admins':
                    $fileName = 'admins_template.xlsx';
                    if (!file_exists($templatePath . $fileName)) {
                        $this->createAdminTemplate();
                    }
                    break;
                    
                default:
                    throw new \Exception('Yanlış şablon növü');
            }
            
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $fileName . '"');
            header('Cache-Control: max-age=0');
            
            readfile($templatePath . $fileName);
            exit;
            
        } catch (\Exception $e) {
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}