<?php

namespace App\Services\Excel;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;
use App\Domain\Entities\School;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataExport
{
    private Spreadsheet $spreadsheet;
    private Worksheet $worksheet;
    private int $currentColumn = 1; // A sütunundan başlayırıq
    private array $columnMap = [];

    public function export(array $categoryIds): Spreadsheet
    {
        $this->initializeSpreadsheet();
        $this->prepareHeaders($categoryIds);
        $this->fillData($categoryIds);

        return $this->spreadsheet;
    }

    private function initializeSpreadsheet(): void
    {
        $this->spreadsheet = new Spreadsheet();
        $this->worksheet = $this->spreadsheet->getActiveSheet();
        
        // İlk sütun həmişə məktəb adı olacaq
        $this->worksheet->setCellValue('A1', 'Məktəb');
        $this->currentColumn = 2; // B sütunundan başlayacağıq
    }

    private function prepareHeaders(array $categoryIds): void
    {
        $columns = Column::whereIn('category_id', $categoryIds)
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        foreach ($columns as $column) {
            $cellAddress = $this->getColumnAddress($this->currentColumn) . '1';
            $this->worksheet->setCellValue($cellAddress, $column->name);
            
            // Sütun mapping-i saxlayırıq
            $this->columnMap[$column->id] = $this->currentColumn;
            $this->currentColumn++;
        }
    }

    private function fillData(array $categoryIds): void
    {
        $schools = School::orderBy('name')->get();
        $currentRow = 2; // Data 2-ci sətirdən başlayır

        foreach ($schools as $school) {
            // Məktəb adını yazırıq
            $this->worksheet->setCellValue('A' . $currentRow, $school->name);

            // Bu məktəbin təsdiqlənmiş məlumatlarını alırıq
            $dataValues = DataValue::where('school_id', $school->id)
                ->where('status', 'approved')
                ->whereIn('column_id', array_keys($this->columnMap))
                ->get();

            foreach ($dataValues as $dataValue) {
                $columnLetter = $this->getColumnAddress($this->columnMap[$dataValue->column_id]);
                $this->worksheet->setCellValue($columnLetter . $currentRow, $dataValue->value);
            }

            $currentRow++;
        }
    }

    private function getColumnAddress(int $columnNumber): string
    {
        // Sütun nömrəsini Excel hərfinə çevirir (1 -> A, 2 -> B, etc.)
        $dividend = $columnNumber;
        $columnName = '';

        while ($dividend > 0) {
            $modulo = ($dividend - 1) % 26;
            $columnName = chr(65 + $modulo) . $columnName;
            $dividend = (int)(($dividend - $modulo) / 26);
        }

        return $columnName;
    }
}