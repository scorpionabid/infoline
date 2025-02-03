<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\V1\BaseController;
use App\Services\Excel\DataExport;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExcelController extends BaseController
{
    private DataExport $exporter;

    public function __construct(DataExport $exporter)
    {
        $this->exporter = $exporter;
    }

    public function export(Request $request): StreamedResponse
    {
        $request->validate([
            'categories' => ['required', 'array'],
            'categories.*' => ['required', 'exists:categories,id']
        ], [
            'categories.required' => 'Ən azı bir kateqoriya seçilməlidir',
            'categories.*.exists' => 'Seçilmiş kateqoriya mövcud deyil'
        ]);

        $spreadsheet = $this->exporter->export($request->categories);
        
        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 'export.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}