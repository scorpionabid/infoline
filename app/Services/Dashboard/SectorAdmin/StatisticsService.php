<?php
// app/Services/Dashboard/SectorAdmin/StatisticsService.php

namespace App\Services\Dashboard\SectorAdmin;

use App\Domain\Entities\School;
use App\Domain\Entities\DataValue;
use App\Domain\Entities\Column;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    public function getSectorStatistics(int $sectorId): array
    {
        $totalSchools = School::where('sector_id', $sectorId)->count();
        $activeSchools = School::where('sector_id', $sectorId)
            ->where('status', true)
            ->count();

        $totalColumns = Column::count();
        $totalRequiredColumns = Column::where('is_required', true)->count();

        // Bütün məktəblərin orta doldurma faizi
        $completionRate = $this->calculateSectorCompletionRate($sectorId);

        // Son 7 gün ərzində dəyişiklik edilmiş məktəblərin sayı
        $activeSchoolsLastWeek = School::where('sector_id', $sectorId)
            ->whereHas('dataValues', function($query) {
                $query->where('updated_at', '>=', Carbon::now()->subDays(7));
            })
            ->count();

        return [
            'total_schools' => $totalSchools,
            'active_schools' => $activeSchools,
            'completion_rate' => $completionRate,
            'active_schools_last_week' => $activeSchoolsLastWeek,
            'total_columns' => $totalColumns,
            'required_columns' => $totalRequiredColumns
        ];
    }

    public function getUpcomingDeadlines(int $sectorId, int $days = 7): array
    {
        $deadline = Carbon::now()->addDays($days);

        $upcomingColumns = Column::whereDate('end_date', '<=', $deadline)
            ->whereDate('end_date', '>=', Carbon::now())
            ->with(['dataValues' => function($query) use ($sectorId) {
                $query->whereHas('school', function($q) use ($sectorId) {
                    $q->where('sector_id', $sectorId);
                });
            }])
            ->get();

        $result = [];
        foreach ($upcomingColumns as $column) {
            $incompleteSchools = School::where('sector_id', $sectorId)
                ->whereDoesntHave('dataValues', function($query) use ($column) {
                    $query->where('column_id', $column->id);
                })
                ->get();

            if ($incompleteSchools->count() > 0) {
                $result[] = [
                    'column' => $column,
                    'deadline' => $column->end_date,
                    'incomplete_schools' => $incompleteSchools,
                    'completion_rate' => $this->calculateColumnCompletionRate($column->id, $sectorId)
                ];
            }
        }

        return $result;
    }

    private function calculateSectorCompletionRate(int $sectorId): float
    {
        $schools = School::where('sector_id', $sectorId)->get();
        if ($schools->isEmpty()) {
            return 0;
        }

        $totalRate = 0;
        foreach ($schools as $school) {
            $totalRate += $this->calculateSchoolCompletionRate($school->id);
        }

        return round($totalRate / $schools->count(), 2);
    }

    private function calculateSchoolCompletionRate(int $schoolId): float
    {
        $totalColumns = Column::count();
        if ($totalColumns === 0) {
            return 0;
        }

        $filledColumns = DataValue::where('school_id', $schoolId)->count();
        return round(($filledColumns / $totalColumns) * 100, 2);
    }

    private function calculateColumnCompletionRate(int $columnId, int $sectorId): float
    {
        $totalSchools = School::where('sector_id', $sectorId)->count();
        if ($totalSchools === 0) {
            return 0;
        }

        $filledSchools = DataValue::where('column_id', $columnId)
            ->whereHas('school', function($query) use ($sectorId) {
                $query->where('sector_id', $sectorId);
            })
            ->count();

        return round(($filledSchools / $totalSchools) * 100, 2);
    }
}