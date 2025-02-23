<?php

namespace App\Exports;

use App\Domain\Entities\School;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class SchoolsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $schools;

    public function __construct($schools)
    {
        $this->schools = $schools;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->schools;
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
            'Ünvan',
            'Status',
            'Admin',
            'Tamamlanma %'
        ];
    }

    /**
     * @param mixed $school
     * @return array
     */
    public function map($school): array
    {
        return [
            $school->name,
            $school->utis_code,
            $school->sector->region->name ?? '-',
            $school->sector->name ?? '-',
            config('enums.school_types.' . $school->type, '-'),
            $school->phone ?? '-',
            $school->email ?? '-',
            $school->address ?? '-',
            $school->status ? 'Aktiv' : 'Deaktiv',
            $school->admin->name ?? '-',
            $school->data_completion_percentage . '%'
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        // First row style
        $sheet->getStyle('A1:K1')->applyFromArray([
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

        // Auto size columns
        foreach(range('A', 'K') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Add borders
        $sheet->getStyle('A1:K' . ($this->schools->count() + 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                ]
            ]
        ]);
    }
}