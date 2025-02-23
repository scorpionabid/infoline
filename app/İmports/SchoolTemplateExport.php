<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SchoolTemplateExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            [
                'Nümunə Məktəb',
                'UTIS123456',
                'Bakı',
                'Sabunçu',
                'Ümumi orta məktəb',
                '+994501234567',
                'mekteb@example.com',
                'Bakı şəhəri, Sabunçu rayonu'
            ]
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Məktəbin Adı',
            'UTİS Kodu',
            'Region',
            'Sektor',
            'Məktəb Tipi',
            'Telefon',
            'Email',
            'Ünvan'
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // Heading style
        $sheet->getStyle('A1:H1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 12
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'E2E8F0'
                ]
            ]
        ]);

        // Example row style
        $sheet->getStyle('A2:H2')->applyFromArray([
            'font' => [
                'italic' => true,
                'color' => [
                    'rgb' => '666666'
                ]
            ]
        ]);

        // Auto size columns
        foreach(range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders
        $sheet->getStyle('A1:H2')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ]);

        // Add data validation for Region and Sector
        $sheet->setDataValidation(
            'C2:C1000',
            [
                'type' => 'list',
                'allowBlank' => false,
                'showDropDown' => true,
                'formula1' => '"Bakı,Sumqayıt,Gəncə,Mingəçevir"'
            ]
        );

        // Add data validation for School Type
        $sheet->setDataValidation(
            'E2:E1000',
            [
                'type' => 'list',
                'allowBlank' => false,
                'showDropDown' => true,
                'formula1' => '"' . implode(',', config('enums.school_types')) . '"'
            ]
        );

        // Add comments
        $sheet->getComment('B2')->getText()->createTextRun('UTİS kodu unikal olmalıdır');
        $sheet->getComment('F2')->getText()->createTextRun('Nümunə format: +994501234567');
        $sheet->getComment('G2')->getText()->createTextRun('Düzgün email formatında olmalıdır');
    }
}