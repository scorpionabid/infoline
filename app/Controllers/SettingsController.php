<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Column;
use App\Models\School;
use App\Models\User;
use App\Core\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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
        try {
            $columns = $this->columnModel->getAll();
            if (!$columns['success']) {
                error_log("Error loading columns: " . ($columns['message'] ?? 'Unknown error'));
            }

            $schools = $this->schoolModel->getAll();
            $schoolAdmins = $this->userModel->getAllSchoolAdmins();

            return View::render('settings/index', [
                'columns' => $columns,
                'schools' => $schools,
                'schoolAdmins' => $schoolAdmins
            ]);
        } catch (\Exception $e) {
            error_log("Error in SettingsController->index: " . $e->getMessage());
            return View::render('settings/index', [
                'columns' => ['success' => false, 'data' => [], 'message' => 'Sistem xətası baş verdi'],
                'schools' => [],
                'schoolAdmins' => []
            ]);
        }
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
                'is_active' => filter_input(INPUT_POST, 'is_active', FILTER_VALIDATE_BOOLEAN) ?? true,
                'category_id' => filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT)
            ];

            // Məcburi sahələri yoxla
            if (empty($data['name']) || empty($data['type'])) {
                return $this->json(['error' => 'Ad və tip sahələri məcburidir']);
            }

            // Kateqoriya seçilməyibsə default kateqoriyanı təyin et
            if (empty($data['category_id'])) {
                $categoryModel = new \App\Models\Category();
                $defaultCategory = $categoryModel->getDefaultCategory();
                if ($defaultCategory) {
                    $data['category_id'] = $defaultCategory['id'];
                }
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

    public function getColumn() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
                return $this->json(['error' => 'Invalid request method']);
            }

            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                return $this->json(['error' => 'Düzgün sütun ID-si daxil edilməyib']);
            }

            $column = $this->columnModel->getById($id);
            if (!$column) {
                return $this->json(['error' => 'Sütun tapılmadı']);
            }

            return $this->json([
                'success' => true,
                'data' => $column
            ]);
        } catch (\Exception $e) {
            error_log("Error in getColumn: " . $e->getMessage());
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

            // Məlumatları yoxla və təmizlə
            $data = [
                'name' => htmlspecialchars(trim($_POST['name']), ENT_QUOTES, 'UTF-8'),
                'type' => htmlspecialchars(trim($_POST['type']), ENT_QUOTES, 'UTF-8'),
                'deadline' => $this->formatDateTime(trim($_POST['deadline'])),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Məcburi sahələri yoxla
            if (empty($data['name']) || empty($data['type'])) {
                return $this->json(['error' => 'Ad və tip sahələri məcburidir']);
            }

            $result = $this->columnModel->update($id, $data);
            
            if ($result) {
                return $this->json([
                    'success' => true,
                    'message' => 'Sütun uğurla yeniləndi'
                ]);
            } else {
                return $this->json(['error' => 'Sütunu yeniləmək mümkün olmadı']);
            }
        } catch (\Exception $e) {
            error_log("Error in updateColumn: " . $e->getMessage());
            return $this->json(['error' => 'Sistem xətası baş verdi']);
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

            // Sütunu yoxlayırıq
            $column = $this->columnModel->getById($id);
            if (!$column) {
                return $this->json(['error' => 'Sütun tapılmadı']);
            }

            // Sütunu silirik
            $result = $this->columnModel->delete($id);
            
            if ($result) {
                return $this->json([
                    'success' => true,
                    'message' => 'Sütun uğurla silindi'
                ]);
            } else {
                return $this->json(['error' => 'Sütunu silmək mümkün olmadı']);
            }
        } catch (\Exception $e) {
            error_log("Error in deleteColumn: " . $e->getMessage());
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

    public function downloadTemplate($type = null) {
        error_log("\n==== Download Template Debug ====");
        error_log("Type: " . $type);
        error_log("Current Directory: " . __DIR__);
        error_log("Template Directory: " . __DIR__ . "/../../public/templates");
        
        if (!in_array($type, ['schools', 'admins'])) {
            error_log("Invalid template type: " . $type);
            header("HTTP/1.0 404 Not Found");
            exit('Template not found');
        }

        $templatePath = __DIR__ . "/../../public/templates/{$type}_template.xlsx";
        error_log("Template path: " . $templatePath);
        
        // Template qovluğunu yoxla və yarat
        $templateDir = dirname($templatePath);
        if (!file_exists($templateDir)) {
            error_log("Creating template directory: " . $templateDir);
            mkdir($templateDir, 0777, true);
        }
        
        // Əgər şablon faylı varsa, onu sil
        if (file_exists($templatePath)) {
            error_log("Deleting existing template file: " . $templatePath);
            unlink($templatePath);
        }
        
        // Yeni şablon faylı yarat
        error_log("Creating new template file...");
        try {
            if ($type === 'schools') {
                $result = $this->createSchoolTemplate();
                error_log("School template creation result: " . ($result ? "success" : "failed"));
            } else {
                $result = $this->createAdminTemplate();
                error_log("Admin template creation result: " . ($result ? "success" : "failed"));
            }
        } catch (\Exception $e) {
            error_log("Error creating template: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            header("HTTP/1.0 500 Internal Server Error");
            exit('Error creating template: ' . $e->getMessage());
        }

        if (!file_exists($templatePath)) {
            error_log("Template file still does not exist after creation attempt");
            header("HTTP/1.0 404 Not Found");
            exit('Template file could not be created');
        }

        error_log("Sending template file: " . $templatePath);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $type . '_template.xlsx"');
        header('Content-Length: ' . filesize($templatePath));
        header('Cache-Control: max-age=0');

        readfile($templatePath);
        exit;
    }

    private function createSchoolTemplate() {
        error_log("Creating school template...");
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers based on actual database structure
            $headers = [
                'A1' => 'Məktəb Adı',  // name field from schools table
            ];
            
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }
            
            // Add sample data
            $sampleData = [
                ['Nümunə Məktəb'],  // Example school name
            ];
            
            $row = 2;
            foreach ($sampleData as $data) {
                $sheet->fromArray($data, null, 'A' . $row);
                $row++;
            }
            
            // Auto-size columns
            foreach (range('A', 'A') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
            
            // Add note about required fields
            $sheet->setCellValue('A4', 'Qeyd: Məktəb adı mütləq doldurulmalıdır');
            $sheet->getStyle('A4')->getFont()->setItalic(true);
            
            $templatePath = __DIR__ . '/../../public/templates/schools_template.xlsx';
            error_log("Saving school template to: " . $templatePath);
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($templatePath);
            
            error_log("School template created successfully");
            return true;
        } catch (\Exception $e) {
            error_log("Error in createSchoolTemplate: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    private function createAdminTemplate() {
        error_log("Creating admin template...");
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set headers based on actual database structure
            $headers = [
                'A1' => 'Məktəb Kodu',      // For linking with school
                'B1' => 'İstifadəçi adı',   // username field
                'C1' => 'Ad',               // name field
                'D1' => 'Şifrə',            // password field
            ];
            
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
                $sheet->getStyle($cell)->getFont()->setBold(true);
            }
            
            // Add sample data
            $sampleData = [
                ['SCH001', 'admin_user', 'Admin Ad Soyad', '123456'],
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
            
            // Add notes about required fields and defaults
            $sheet->setCellValue('A4', 'Qeydlər:');
            $sheet->setCellValue('A5', '1. Məktəb kodu mövcud məktəbə aid olmalıdır (məs: SCH001)');
            $sheet->setCellValue('A6', '2. İstifadəçi adı unikal olmalıdır');
            $sheet->setCellValue('A7', '3. Şifrə boş buraxılarsa, default olaraq "123456" təyin olunacaq');
            $sheet->getStyle('A4:A7')->getFont()->setItalic(true);
            
            $templatePath = __DIR__ . '/../../public/templates/admins_template.xlsx';
            error_log("Saving admin template to: " . $templatePath);
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($templatePath);
            
            error_log("Admin template created successfully");
            return true;
        } catch (\Exception $e) {
            error_log("Error in createAdminTemplate: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    // Kateqoriya əməliyyatları
    public function getCategories() {
        try {
            $categoryModel = new \App\Models\Category();
            $categories = $categoryModel->getAll();
            
            return $this->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            error_log("Error in getCategories: " . $e->getMessage());
            return $this->json([
                'success' => false,
                'error' => 'Kateqoriyalar yüklənmədi: ' . $e->getMessage()
            ]);
        }
    }

    public function addCategory() {
        try {
            error_log("addCategory: Starting...");
            error_log("POST data: " . print_r($_POST, true));
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->json(['error' => 'Invalid request method']);
            }

            $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');

            error_log("Sanitized data - name: $name, description: $description");

            if (empty($name)) {
                error_log("Error: Category name is empty");
                return $this->json(['error' => 'Kateqoriya adı daxil edilməlidir']);
            }

            $categoryModel = new \App\Models\Category();
            
            // Eyni adlı kateqoriyanın varlığını yoxla
            $existing = $categoryModel->findByName($name);
            if ($existing) {
                error_log("Error: Category with name '$name' already exists");
                return $this->json(['error' => 'Bu adda kateqoriya artıq mövcuddur']);
            }

            $data = [
                'name' => $name,
                'description' => $description,
                'is_default' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            error_log("Attempting to create category with data: " . print_r($data, true));

            $result = $categoryModel->create($data);
            
            if ($result) {
                error_log("Category created successfully with ID: $result");
                return $this->json([
                    'success' => true,
                    'message' => 'Kateqoriya uğurla əlavə edildi',
                    'id' => $result
                ]);
            } else {
                error_log("Failed to create category");
                return $this->json(['error' => 'Kateqoriya əlavə edilmədi']);
            }
        } catch (\Exception $e) {
            error_log("Error in addCategory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return $this->json(['error' => 'Sistem xətası baş verdi']);
        }
    }

    public function updateCategory() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->json(['error' => 'Invalid request method']);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                return $this->json(['error' => 'Düzgün kateqoriya ID-si daxil edilməyib']);
            }

            $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');

            if (empty($name)) {
                return $this->json(['error' => 'Kateqoriya adı daxil edilməlidir']);
            }

            $categoryModel = new \App\Models\Category();
            
            // Kateqoriyanı yoxla
            $category = $categoryModel->getById($id);
            if (!$category) {
                return $this->json(['error' => 'Kateqoriya tapılmadı']);
            }

            // Eyni adlı başqa kateqoriyanın varlığını yoxla
            $existing = $categoryModel->findByName($name);
            if ($existing && $existing['id'] != $id) {
                return $this->json(['error' => 'Bu adda kateqoriya artıq mövcuddur']);
            }

            $data = [
                'name' => $name,
                'description' => $description,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $result = $categoryModel->update($id, $data);
            
            if ($result) {
                return $this->json([
                    'success' => true,
                    'message' => 'Kateqoriya uğurla yeniləndi'
                ]);
            } else {
                return $this->json(['error' => 'Kateqoriya yenilənmədi']);
            }
        } catch (\Exception $e) {
            error_log("Error in updateCategory: " . $e->getMessage());
            return $this->json(['error' => 'Sistem xətası baş verdi']);
        }
    }

    public function deleteCategory() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                return $this->json(['error' => 'Invalid request method']);
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) {
                return $this->json(['error' => 'Düzgün kateqoriya ID-si daxil edilməyib']);
            }

            $categoryModel = new \App\Models\Category();
            
            // Kateqoriyanı yoxla
            $category = $categoryModel->getById($id);
            if (!$category) {
                return $this->json(['error' => 'Kateqoriya tapılmadı']);
            }

            // Default kateqoriyanı silməyə icazə vermə
            if ($category['is_default']) {
                return $this->json(['error' => 'Default kateqoriyanı silmək olmaz']);
            }

            // Kateqoriyaya aid sütunları yoxla
            $columnModel = new Column();
            $columns = $columnModel->getAllByCategoryId($id);
            if (!empty($columns['data'])) {
                return $this->json(['error' => 'Bu kateqoriyaya aid sütunlar var. Əvvəlcə onları silməlisiniz.']);
            }

            $result = $categoryModel->delete($id);
            
            if ($result) {
                return $this->json([
                    'success' => true,
                    'message' => 'Kateqoriya uğurla silindi'
                ]);
            } else {
                return $this->json(['error' => 'Kateqoriya silinmədi']);
            }
        } catch (\Exception $e) {
            error_log("Error in deleteCategory: " . $e->getMessage());
            return $this->json(['error' => 'Sistem xətası baş verdi']);
        }
    }
}