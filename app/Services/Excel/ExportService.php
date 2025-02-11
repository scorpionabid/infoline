<?php

namespace App\Services\Excel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportService
{
    public function downloadTemplate(string $type): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        match($type) {
            'schools' => $this->createSchoolTemplate($sheet),
            'admins' => $this->createAdminTemplate($sheet),
            default => throw new \InvalidArgumentException('Invalid template type')
        };

        $writer = new Xlsx($spreadsheet);
        $path = storage_path("app/templates/{$type}_template.xlsx");
        $writer->save($path);

        return response()->download($path);
    }

    private function createSchoolTemplate($sheet): void
    {
        $headers = ['Məktəb adı', 'Sektor ID', 'UTIS kod', 'Telefon', 'Email'];
        $sheet->fromArray($headers, null, 'A1');
        
        // Format cells
        foreach(range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function createAdminTemplate($sheet): void
    {
        // Admin template məntiqi
    }
}