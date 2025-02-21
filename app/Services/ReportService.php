<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
    /**
     * Məlumatları Excel formatında export edir
     */
    public function exportToExcel($data, $columns, $title)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Başlıq
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:' . $this->getColumnLetter(count($columns)) . '1');

        // Sütun başlıqları
        $col = 'A';
        $row = 2;
        foreach ($columns as $column) {
            $sheet->setCellValue($col . $row, $column);
            $col++;
        }

        // Məlumatlar
        $row = 3;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($item as $value) {
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Formatlaşdırma
        $sheet->getStyle('A1:' . $this->getColumnLetter(count($columns)) . '1')
            ->getFont()
            ->setBold(true);
        $sheet->getStyle('A2:' . $this->getColumnLetter(count($columns)) . '2')
            ->getFont()
            ->setBold(true);

        // Faylı yaratma
        $writer = new Xlsx($spreadsheet);
        $filename = time() . '_report.xlsx';
        $path = storage_path('app/public/reports/' . $filename);
        $writer->save($path);

        return $path;
    }

    /**
     * Məlumatları PDF formatında export edir
     */
    public function exportToPdf($data, $columns, $title)
    {
        $pdf = PDF::loadView('reports.pdf', compact('data', 'columns', 'title'));
        
        $filename = time() . '_report.pdf';
        $path = storage_path('app/public/reports/' . $filename);
        $pdf->save($path);

        return $path;
    }

    /**
     * Sütun nömrəsini hərfə çevirir (1=A, 2=B, etc.)
     */
    private function getColumnLetter($number)
    {
        $letter = '';
        while ($number > 0) {
            $temp = ($number - 1) % 26;
            $letter = chr(65 + $temp) . $letter;
            $number = floor(($number - 1) / 26);
        }
        return $letter;
    }
}