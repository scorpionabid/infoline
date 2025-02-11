<?php

namespace App\Services\Excel;

use App\Domain\Entities\{School, User, Sector};
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportService 
{
    public function import(UploadedFile $file, string $type): array
    {
        try {
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Remove header
            array_shift($rows);
            
            return match($type) {
                'schools' => $this->importSchools($rows),
                'admins' => $this->importAdmins($rows),
                default => throw new \InvalidArgumentException('Invalid import type')
            };
        } catch (\Exception $e) {
            return [
                'error' => 'Import xətası: ' . $e->getMessage()
            ];
        }
    }

    private function importSchools(array $rows): array
    {
        $imported = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            try {
                School::create([
                    'name' => $row[0],
                    'sector_id' => $row[1],
                    'utis_code' => $row[2],
                    'phone' => $row[3],
                    'email' => $row[4]
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Sətir {$i}: " . $e->getMessage();
            }
        }

        return [
            'success' => true,
            'imported' => $imported,
            'errors' => $errors
        ];
    }

    private function importAdmins(array $rows): array 
    {
        // Admin import məntiqi
    }
}