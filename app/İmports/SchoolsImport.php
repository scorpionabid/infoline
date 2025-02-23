<?php

namespace App\Imports;

use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SchoolsImport implements ToModel, WithHeadingRow, WithValidation
{
    private $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => []
    ];

    /**
     * Transform Excel row to School model
     *
     * @param array $row
     * @return \App\Domain\Entities\School|null
     */
    public function model(array $row)
    {
        try {
            // Find sector by name and region
            $sector = Sector::whereHas('region', function($query) use ($row) {
                $query->where('name', $row['region']);
            })->where('name', $row['sektor'])->first();

            if (!$sector) {
                $this->results['failed']++;
                $this->results['errors'][] = "Sətir {$row['row_num']}: Region və ya sektor tapılmadı: {$row['region']} - {$row['sektor']}";
                return null;
            }

            // Create school
            $school = new School([
                'name' => $row['mektebin_adi'],
                'utis_code' => $row['utis_kodu'],
                'type' => $this->getSchoolType($row['mekteb_tipi']),
                'sector_id' => $sector->id,
                'phone' => $row['telefon'] ?? null,
                'email' => $row['email'] ?? null,
                'address' => $row['unvan'] ?? null,
                'status' => 1
            ]);

            $this->results['success']++;
            return $school;

        } catch (\Exception $e) {
            $this->results['failed']++;
            $this->results['errors'][] = "Sətir {$row['row_num']}: " . $e->getMessage();
            Log::error('School import error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get validation rules
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'mektebin_adi' => 'required|string|max:255',
            'utis_kodu' => 'required|string|unique:schools,utis_code',
            'mekteb_tipi' => 'required|string',
            'region' => 'required|string',
            'sektor' => 'required|string',
            'telefon' => 'nullable|string',
            'email' => 'nullable|email',
            'unvan' => 'nullable|string'
        ];
    }

    /**
     * Get custom validation messages
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'mektebin_adi.required' => 'Məktəbin adı tələb olunur',
            'utis_kodu.required' => 'UTİS kodu tələb olunur',
            'utis_kodu.unique' => 'Bu UTİS kodu artıq mövcuddur',
            'mekteb_tipi.required' => 'Məktəb tipi tələb olunur',
            'region.required' => 'Region tələb olunur',
            'sektor.required' => 'Sektor tələb olunur',
            'email.email' => 'Düzgün email formatı daxil edin'
        ];
    }

    /**
     * Get school type from config based on Excel value
     *
     * @param string $type
     * @return int|null
     */
    private function getSchoolType(string $type)
    {
        $types = array_flip(config('enums.school_types'));
        return $types[$type] ?? null;
    }

    /**
     * Get import results
     *
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }
}