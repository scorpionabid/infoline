<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SchoolDataExport implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function map($row): array
    {
        return [
            $row->school->name,
            $row->school->sector->name,
            $row->category->name,
            $row->status_text,
            $row->completion_percentage . '%',
            $row->updated_at->format('d.m.Y H:i')
        ];
    }

    public function headings(): array
    {
        return [
            'Məktəb',
            'Sektor',
            'Kateqoriya',
            'Status',
            'Tamamlanma',
            'Son yenilənmə'
        ];
    }
}