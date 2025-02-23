<?php
// app/Services/Dashboard/SectorAdmin/ReportService.php

namespace App\Services\Dashboard\SectorAdmin;

use App\Domain\Entities\School;
use App\Domain\Entities\Category;
use App\Domain\Entities\DataValue;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getGeneralStatistics($sectorId)
    {
        $schools = School::where('sector_id', $sectorId)->get();
        
        // Son 30 gün ərzində doldurulmuş məlumatlar
        $lastMonthData = DataValue::whereHas('school', function($query) use ($sectorId) {
            $query->where('sector_id', $sectorId);
        })
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->count();

        return [
            'total_schools' => $schools->count(),
            'active_schools' => $schools->where('status', true)->count(),
            'average_completion' => $this->calculateAverageCompletion($schools),
            'last_month_data' => $lastMonthData
        ];
    }

    public function getSchoolsComparison($sectorId)
    {
        return School::where('sector_id', $sectorId)
            ->withCount('dataValues')
            ->with(['dataValues' => function($query) {
                $query->select('school_id', 'created_at')
                    ->latest()
                    ->take(1);
            }])
            ->get()
            ->map(function($school) {
                return [
                    'name' => $school->name,
                    'data_count' => $school->data_values_count,
                    'completion_rate' => $this->calculateSchoolCompletion($school),
                    'last_update' => $school->dataValues->first()?->created_at,
                    'trend' => $this->calculateSchoolTrend($school)
                ];
            });
    }

    public function getCategoryAnalysis($sectorId)
    {
        return Category::with(['columns.dataValues' => function($query) use ($sectorId) {
            $query->whereHas('school', function($q) use ($sectorId) {
                $q->where('sector_id', $sectorId);
            });
        }])
        ->get()
        ->map(function($category) use ($sectorId) {
            $totalPossibleValues = $category->columns->count() * 
                School::where('sector_id', $sectorId)->count();
            
            $actualValues = $category->columns
                ->flatMap->dataValues
                ->count();

            return [
                'name' => $category->name,
                'completion_rate' => $totalPossibleValues > 0 ? 
                    ($actualValues / $totalPossibleValues) * 100 : 0,
                'columns_count' => $category->columns->count(),
                'required_columns' => $category->columns->where('is_required', true)->count()
            ];
        });
    }

    public function getMonthlyTrend($sectorId)
    {
        $startDate = Carbon::now()->subMonths(11)->startOfMonth();
        
        return DataValue::whereHas('school', function($query) use ($sectorId) {
            $query->where('sector_id', $sectorId);
        })
        ->where('created_at', '>=', $startDate)
        ->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->map(function($item) {
            return [
                'month' => Carbon::createFromFormat('Y-m', $item->month)->format('M Y'),
                'count' => $item->count
            ];
        });
    }

    public function generateExcelReport($sectorId, $filters)
    {
        // Excel export implementation
    }

    private function calculateAverageCompletion($schools)
    {
        if ($schools->isEmpty()) {
            return 0;
        }

        $totalCompletionRate = 0;
        foreach ($schools as $school) {
            $totalCompletionRate += $this->calculateSchoolCompletion($school);
        }

        return round($totalCompletionRate / $schools->count(), 2);
    }

    private function calculateSchoolCompletion($school)
    {
        $totalColumns = Column::count();
        if ($totalColumns === 0) {
            return 0;
        }

        return round(($school->data_values_count / $totalColumns) * 100, 2);
    }

    private function calculateSchoolTrend($school)
    {
        // Son 3 ayın məlumatları
        $monthlyData = DataValue::where('school_id', $school->id)
            ->where('created_at', '>=', Carbon::now()->subMonths(3))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Trend hesablanması
        if ($monthlyData->count() < 2) {
            return 'stable';
        }

        $firstMonth = $monthlyData->first()->count;
        $lastMonth = $monthlyData->last()->count;

        if ($lastMonth > $firstMonth) {
            return 'increasing';
        } elseif ($lastMonth < $firstMonth) {
            return 'decreasing';
        }

        return 'stable';
    }
}