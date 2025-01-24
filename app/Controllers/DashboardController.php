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
        // Start session if not started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        error_log("Export request received");
        error_log("Session data: " . print_r($_SESSION, true));

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
            error_log("Authorization failed: user_id=" . ($_SESSION['user_id'] ?? 'not set') . ", role=" . ($_SESSION['role'] ?? 'not set'));
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'İcazə yoxdur']);
            return;
        }

        try {
            error_log("Starting Excel export process");
            
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Get data
            $schools = $this->schoolModel->getAll();
            error_log("Schools data: " . count($schools) . " records found");
            
            $columns = $this->columnModel->getAll();
            error_log("Columns data: " . count($columns) . " records found");
            
            $data = $this->dataModel->getAllSchoolData();
            error_log("School data: " . count($data) . " records found");
            
            // Set headers
            $sheet->setCellValue('A1', 'Məktəb Kodu');
            $sheet->setCellValue('B1', 'Məktəb Adı');
            $sheet->setCellValue('C1', 'Region');
            $sheet->setCellValue('D1', 'Ünvan');
            $sheet->setCellValue('E1', 'Telefon');
            $sheet->setCellValue('F1', 'Direktor');
            
            $col = 'G';
            foreach ($columns as $column) {
                $sheet->setCellValue($col . '1', $column['name']);
                $col++;
            }
            
            // Fill data
            $row = 2;
            foreach ($schools as $school) {
                $sheet->setCellValue('A' . $row, $school['code']);
                $sheet->setCellValue('B' . $row, $school['name']);
                $sheet->setCellValue('C' . $row, $school['region']);
                $sheet->setCellValue('D' . $row, $school['address']);
                $sheet->setCellValue('E' . $row, $school['phone']);
                $sheet->setCellValue('F' . $row, $school['principal_name']);
                
                $col = 'G';
                foreach ($columns as $column) {
                    $value = '';
                    foreach ($data as $item) {
                        if ($item['school_id'] == $school['id'] && $item['column_id'] == $column['id']) {
                            $value = $item['value'];
                            break;
                        }
                    }
                    $sheet->setCellValue($col . $row, $value);
                    $col++;
                }
                $row++;
            }
            
            // Style the header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4F81BD']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ];
            
            $sheet->getStyle('A1:' . $col . '1')->applyFromArray($headerStyle);
            
            // Auto-size columns
            foreach (range('A', $col) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
            
            // Create Excel file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $filename = 'mekteb_melumatlari_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filepath = __DIR__ . '/../../public/exports/' . $filename;
            
            // Ensure exports directory exists
            if (!file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0777, true);
            }
            
            $writer->save($filepath);
            
            echo json_encode([
                'success' => true,
                'filename' => $filename,
                'url' => '/exports/' . $filename
            ]);
            
        } catch (\Exception $e) {
            error_log("Excel Export Error: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => 'Export zamanı xəta baş verdi']);
        }
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