<?php

namespace App\Services\Excel\Templates;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class TemplateGenerator
{
    private Spreadsheet $spreadsheet;
    private Worksheet $dataSheet;
    private Worksheet $instructionSheet;
    private int $currentColumn = 1;

    public function generate(array $categoryIds): Spreadsheet
    {
        $this->initializeSpreadsheet();
        $this->createInstructionSheet();
        $this->prepareDataSheet($categoryIds);
        $this->addSampleData();

        return $this->spreadsheet;
    }

    private function initializeSpreadsheet(): void
    {
        $this->spreadsheet = new Spreadsheet();
        
        // Təlimat səhifəsi
        $this->instructionSheet = $this->spreadsheet->getActiveSheet();
        $this->instructionSheet->setTitle('Təlimatlar');
        
        // Data səhifəsi
        $this->dataSheet = $this->spreadsheet->createSheet();
        $this->dataSheet->setTitle('Məlumatlar');
    }

    private function createInstructionSheet(): void
    {
        $this->instructionSheet->setCellValue('A1', 'Doldurma qaydaları');
        $this->instructionSheet->setCellValue('A3', '1. Məlumatları yalnız "Məlumatlar" səhifəsində qeyd edin');
        $this->instructionSheet->setCellValue('A4', '2. Bütün məcburi xanaları doldurun');
        $this->instructionSheet->setCellValue('A5', '3. Məlumatları düzgün formatda daxil edin:');
        $this->instructionSheet->setCellValue('B6', '- Rəqəm: Yalnız rəqəm daxil edin');
        $this->instructionSheet->setCellValue('B7', '- Tarix: DD.MM.YYYY formatında daxil edin');
        
        // Başlığı formatlaşdıraq
        $this->instructionSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    }

    private function prepareDataSheet(array $categoryIds): void
    {
        // Məktəb sütunu
        $this->dataSheet->setCellValue('A1', 'Məktəb');
        $this->currentColumn = 2;

        $columns = Column::whereIn('category_id', $categoryIds)
            ->orderBy('id')
            ->get();

        foreach ($columns as $column) {
            $columnLetter = $this->getColumnAddress($this->currentColumn);
            
            // Başlıq
            $this->dataSheet->setCellValue($columnLetter . '1', $column->name);
            
            // Validasiya
            $this->addValidationRule($columnLetter, $column);
            
            $this->currentColumn++;
        }

        // Başlıqları formatlaşdıraq
        $lastColumn = $this->getColumnAddress($this->currentColumn - 1);
        $this->dataSheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2EFDA']
            ]
        ]);
    }

    private function addValidationRule(string $column, Column $columnModel): void
    {
    // Düzəliş: Validasiya obyektini yaratmaq və qurmaq
        $validation = $this->dataSheet->getCell($column . '2')->getDataValidation();
    
    // Validasiya qurulması
        $validation->setType(DataValidation::TYPE_NONE); // Default tip
        $validation->setAllowBlank(true);
    
    // Data tipinə görə validation
        switch ($columnModel->data_type) {
            case 'number':
                $validation->setType(DataValidation::TYPE_DECIMAL);
                $validation->setOperator(DataValidation::OPERATOR_BETWEEN);
                $validation->setFormula1(-999999999);
                $validation->setFormula2(999999999);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Xəta');
                $validation->setError('Yalnız rəqəm daxil edə bilərsiniz');
                break;
            
            case 'date':
                $validation->setType(DataValidation::TYPE_DATE);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Xəta');
                $validation->setError('Tarix düzgün formatda deyil');
                break;
            
            case 'select':
            case 'multiselect':
                if ($columnModel->choices()->count() > 0) {
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setFormula1('"' . implode(',', $columnModel->choices()->pluck('value')->toArray()) . '"');
                    $validation->setErrorStyle(DataValidation::STYLE_STOP);
                    $validation->setShowErrorMessage(true);
                    $validation->setErrorTitle('Xəta');
                    $validation->setError('Siyahıdan seçim edin');
                }
                break;
        }

    // Validasiyanı sütunun bütün hüceyrələrinə tətbiq edirik
        $this->dataSheet->setDataValidation(
            $column . '2:' . $column . '1000',
            clone $validation
        );
    }

    private function addSampleData(): void
    {
        // Nümunə məktəb
        $this->dataSheet->setCellValue('A2', 'Nümunə Məktəb');
        
        // Digər sütunlar üçün nümunə data
        for ($i = 2; $i < $this->currentColumn; $i++) {
            $columnLetter = $this->getColumnAddress($i);
            $this->dataSheet->setCellValue($columnLetter . '2', 'Nümunə dəyər');
        }

        // Nümunə sətri vurğulayaq
        $lastColumn = $this->getColumnAddress($this->currentColumn - 1);
        $this->dataSheet->getStyle("A2:{$lastColumn}2")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FCE4D6']
            ]
        ]);
    }

    private function getColumnAddress(int $columnNumber): string
    {
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