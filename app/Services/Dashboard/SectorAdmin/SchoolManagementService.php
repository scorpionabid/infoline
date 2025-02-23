<?php
// app/Services/Dashboard/SectorAdmin/SchoolManagementService.php

namespace App\Services\Dashboard\SectorAdmin;

use App\Domain\Entities\School;
use App\Domain\Entities\Column;
use Carbon\Carbon;

class SchoolManagementService
{
    protected $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function getSchoolsWithStatus(int $sectorId)
    {
        return School::where('sector_id', $sectorId)
            ->with(['admin', 'dataValues'])
            ->get()
            ->map(function($school) {
                $status = $this->calculateSchoolStatus($school);
                return array_merge($school->toArray(), [
                    'completion_rate' => $this->calculateCompletionRate($school),
                    'status' => $status,
                    'last_update' => $school->dataValues->max('updated_at'),
                    'missing_required' => $this->getMissingRequiredCount($school)
                ]);
            });
    }

    public function getCriticalSchools(int $sectorId)
    {
        $schools = School::where('sector_id', $sectorId)
            ->with(['dataValues', 'admin'])
            ->get();

        return $schools->filter(function($school) {
            // Məcburi sütunlar doldurulmayıb
            $missingRequired = $this->getMissingRequiredCount($school) > 0;
            
            // Son 30 gün ərzində heç bir yeniləmə yoxdur
            $noRecentUpdates = $school->dataValues
                ->max('updated_at') < Carbon::now()->subDays(30);
            
            // Doldurulma faizi 50%-dən aşağıdır
            $lowCompletionRate = $this->calculateCompletionRate($school) < 50;

            return $missingRequired || $noRecentUpdates || $lowCompletionRate;
        });
    }

    private function calculateSchoolStatus($school)
    {
        $completionRate = $this->calculateCompletionRate($school);
        $missingRequired = $this->getMissingRequiredCount($school);
        
        if ($missingRequired > 0) {
            return 'critical';
        } elseif ($completionRate < 50) {
            return 'warning';
        } elseif ($completionRate < 100) {
            return 'in_progress';
        } else {
            return 'completed';
        }
    }

    private function calculateCompletionRate($school): float
    {
        $totalColumns = Column::count();
        if ($totalColumns === 0) {
            return 0;
        }

        $filledColumns = $school->dataValues->count();
        return round(($filledColumns / $totalColumns) * 100, 2);
    }

    private function getMissingRequiredCount($school): int
    {
        $requiredColumns = Column::where('is_required', true)->get();
        $filledRequiredColumns = $school->dataValues
            ->whereIn('column_id', $requiredColumns->pluck('id'))
            ->count();

        return $requiredColumns->count() - $filledRequiredColumns;
    }
}