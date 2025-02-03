<?php

namespace App\Services\Excel;

use App\Domain\Entities\School;
use App\Domain\Entities\DataValue;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class DataImport
{
    private $spreadsheet;
    private $worksheet;
    private $columnMap = [];
    private ImportResult $result;
    
    public function import(UploadedFile $file, School $school): ImportResult
    {
        $this->result = new ImportResult();
        
        try {
            $this->loadSpreadsheet($file);
            $this->validateStructure();
            $this->processData($school);
        } catch (\Exception $e) {
            $this->result->addError($e->getMessage());
        }

        return $this->result;
    }

    private function loadSpreadsheet(UploadedFile $file): void
    {
        try {
            $this->spreadsheet = IOFactory::load($file->getPathname());
            $this->worksheet = $this->spreadsheet->getActiveSheet();
        } catch (\Exception $e) {
            throw new \Exception('Excel faylı oxuna bilmədi: ' . $e->getMessage());
        }
    }

    private function validateStructure(): void
    {
        // İlk sətir header olmalıdır
        $headerRow = 1;
        $columnCount = $this->worksheet->getHighestDataColumn();
        
        // İlk sütun məktəb adı olmalıdır
        if ($this->worksheet->getCell('A1')->getValue() !== 'Məktəb') {
            throw new \Exception('İlk sütun "Məktəb" olmalıdır');
        }

        // Digər sütunların validasiyası
        for ($col = 'B'; $col <= $columnCount; $col++) {
            $columnName = $this->worksheet->getCell($col . '1')->getValue();
            $column = \App\Domain\Entities\Column::where('name', $columnName)->first();
            
            if (!$column) {
                throw new \Exception("Sütun tapılmadı: {$columnName}");
            }
            
            $this->columnMap[$col] = $column;
        }
    }

    private function processData(School $school): void
    {
        DB::beginTransaction();
        try {
            $rowCount = $this->worksheet->getHighestDataRow();
            
            for ($row = 2; $row <= $rowCount; $row++) {
                foreach ($this->columnMap as $excelColumn => $dbColumn) {
                    $value = $this->worksheet->getCell($excelColumn . $row)->getValue();
                    
                    if (empty($value)) continue;

                    try {
                        DataValue::updateOrCreate(
                            [
                                'school_id' => $school->id,
                                'column_id' => $dbColumn->id
                            ],
                            [
                                'value' => $value,
                                'status' => 'draft'
                            ]
                        );
                        
                        $this->result->incrementImported();
                    } catch (\Exception $e) {
                        $this->result->addRowError($row, "Sətir {$row}, sütun {$excelColumn}: " . $e->getMessage());
                    }
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}