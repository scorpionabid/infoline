<?php

namespace App\Services\Dashboard\SchoolAdmin;

use App\Domain\Entities\School;
use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;

class StatisticsService
{
    public function getSchoolStatistics(int $schoolId): array
    {
        $totalColumns = Column::count();
        $filledColumns = DataValue::where('school_id', $schoolId)->count();
        $emptyColumns = $totalColumns - $filledColumns;
        
        $requiredColumns = Column::where('is_required', true)->count();
        $filledRequiredColumns = DataValue::where('school_id', $schoolId)
            ->whereHas('column', function($query) {
                $query->where('is_required', true);
            })
            ->count();

        $completionRate = $totalColumns > 0 
            ? round(($filledColumns / $totalColumns) * 100, 2) 
            : 0;

        return [
            'total_columns' => $totalColumns,
            'filled_columns' => $filledColumns,
            'empty_columns' => $emptyColumns,
            'required_columns' => $requiredColumns,
            'filled_required_columns' => $filledRequiredColumns,
            'completion_rate' => $completionRate,
            'last_update' => DataValue::where('school_id', $schoolId)
                ->latest()
                ->first()
                ?->updated_at
        ];
    }
}