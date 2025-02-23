<?php

namespace App\Services\Dashboard\SchoolAdmin;

use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;
use Carbon\Carbon;

class DataEntryService
{
    public function getCategoriesWithColumns(int $schoolId)
    {
        return Category::with(['columns' => function($query) {
            $query->whereNull('end_date')
                ->orWhere('end_date', '>', now())
                ->orderBy('order');
        }, 'columns.dataValues' => function($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        }])->get();
    }

    public function getEmptyRequiredColumns(int $schoolId)
    {
        return Column::where('is_required', true)
            ->whereDoesntHave('dataValues', function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->get();
    }

    public function getUpcomingDeadlines(int $schoolId, int $days)
    {
        $deadline = Carbon::now()->addDays($days);
        
        return Column::whereDate('end_date', '<=', $deadline)
            ->whereDate('end_date', '>=', Carbon::now())
            ->whereDoesntHave('dataValues', function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->orderBy('end_date')
            ->get();
    }

    public function getNewlyAddedColumns(int $schoolId, int $days)
    {
        return Column::where('created_at', '>=', Carbon::now()->subDays($days))
            ->whereDoesntHave('dataValues', function($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->get();
    }
    public function saveColumnValue(int $schoolId, int $columnId, $value)
    {
        $column = Column::findOrFail($columnId);
        
        // Məlumatın tipini yoxla
        $this->validateColumnValue($column, $value);
        
        // Məlumatı yadda saxla
        $dataValue = DataValue::updateOrCreate(
            [
                'school_id' => $schoolId,
                'column_id' => $columnId
            ],
            [
                'value' => $value,
                'updated_by' => auth()->id()
            ]
        );

        // Audit log
        activity()
            ->performedOn($dataValue)
            ->withProperties(['old_value' => $dataValue->getOriginal('value')])
            ->log('data_updated');

        return $dataValue;
    }

    public function bulkUpdateValues(int $schoolId, array $values)
    {
        $results = [];
    
        foreach ($values as $value) {
            $results[] = $this->saveColumnValue(
                $schoolId, 
                $value['column_id'], 
                $value['value']
            );
        }
    
        return $results;
    }

    private function validateColumnValue(Column $column, $value)
    {
        switch ($column->data_type) {
        case 'number':
            if (!is_numeric($value)) {
                throw new \InvalidArgumentException("Rəqəm tələb olunur");
            }
            break;
            
        case 'date':
            if (!strtotime($value)) {
                throw new \InvalidArgumentException("Yanlış tarix formatı");
            }
            break;
            
        case 'select':
            if (!in_array($value, $column->options)) {
                throw new \InvalidArgumentException("Yanlış seçim");
            }
            break;
        }
    }
}