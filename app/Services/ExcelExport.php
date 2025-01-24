<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\School;
use App\Models\Column;
use App\Models\DataValue;

class ExcelExport {
    private $spreadsheet;
    private $sheet;
    
    public function __construct() {
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
    }
    
    public function exportData($filters = []) {
        // Get all schools and columns
        $schoolModel = new School();
        $columnModel = new Column();
        $dataModel = new DataValue();
        
        $schools = $schoolModel->getAll();
        $columns = $columnModel->getAll(['is_active' => true]);
        
        // Set headers
        $this->sheet->setCellValue('A1', 'Məktəb Kodu');
        $this->sheet->setCellValue('B1', 'Məktəb Adı');
        $this->sheet->setCellValue('C1', 'Region');
        
        $col = 'D';
        foreach ($columns as $column) {
            $this->sheet->setCellValue($col . '1', $column['name']);
            $col++;
        }
        
        // Fill data
        $row = 2;
        foreach ($schools as $school) {
            $this->sheet->setCellValue('A' . $row, $school['code']);
            $this->sheet->setCellValue('B' . $row, $school['name']);
            $this->sheet->setCellValue('C' . $row, $school['region']);
            
            $col = 'D';
            foreach ($columns as $column) {
                $value = $dataModel->getValue($school['id'], $column['id']);
                $this->sheet->setCellValue($col . $row, $value['value'] ?? '');
                $col++;
            }
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', $col) as $columnID) {
            $this->sheet->getColumnDimension($columnID)->setAutoSize(true);
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
        
        $this->sheet->getStyle('A1:' . $col . '1')->applyFromArray($headerStyle);
        
        // Create the excel file
        $writer = new Xlsx($this->spreadsheet);
        $filename = 'school_data_' . date('Y-m-d_H-i-s') . '.xlsx';
        $filepath = __DIR__ . '/../../public/exports/' . $filename;
        
        // Ensure the exports directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }
        
        $writer->save($filepath);
        
        return $filename;
    }
}