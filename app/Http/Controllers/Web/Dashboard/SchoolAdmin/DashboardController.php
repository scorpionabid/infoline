<?php

namespace App\Http\Controllers\Web\Dashboard\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Domain\Entities\{
    Category,
    DataValue,
    Deadline,
    CategoryAssignment
};
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $school = $user->school;
        $schoolId = $school->id;

        // Aktiv kateqoriyalar və sütunları əldə edirik
        $categories = $this->getFilteredCategories($schoolId);

        // Məktəbin məlumatlarını əldə edirik
        $dataValues = DataValue::where('school_id', $schoolId)->get();
        $columns = $categories->flatMap(function($category) {
            return $category->columns;
        });

        // Hər sütun üçün data value-ları əllə təyin et
        foreach ($columns as $column) {
            // Hər sütun üçün data value-ları əllə təyin et
            $column->dataValues = $dataValues->where('column_id', $column->id);
        }

        // Sütunların tam siyahısı
        $allColumns = $categories->flatMap(function($category) {
            return $category->columns;
        });

        // Toplam sütunlar
        $totalColumns = $allColumns->count();
        
        // Doldurulmuş sütunlar
        $filledColumns = $allColumns->filter(function($column) {
            return $column->dataValues->isNotEmpty();
        })->count();
        
        // Məcburi sütunlar
        $requiredColumns = $allColumns->where('is_required', true);
        $totalRequiredColumns = $requiredColumns->count();
        
        // Doldurulmuş məcburi sütunlar
        $filledRequiredColumns = $requiredColumns->filter(function($column) {
            return $column->dataValues->isNotEmpty();
        })->count();
        
        // Doldurulma faizi
        $completionRate = $totalColumns > 0 ? round(($filledColumns / $totalColumns) * 100) : 0;
        $requiredCompletionRate = $totalRequiredColumns > 0 ? round(($filledRequiredColumns / $totalRequiredColumns) * 100) : 0;
        
        // Yaxınlaşan son tarixlər
        $upcomingDeadlines = $allColumns->where('end_date', '!=', null)
            ->where('end_date', '>', now())
            ->where('end_date', '<', now()->addDays(7))
            ->sortBy('end_date')
            ->take(5);
        
        // Məcburi amma doldurulmamış sütunlar
        $missingRequiredColumns = $requiredColumns->filter(function($column) {
            return $column->dataValues->isEmpty();
        });

        return view('pages.dashboard.school-admin.index', compact(
            'categories',
            'totalColumns',
            'filledColumns',
            'totalRequiredColumns',
            'filledRequiredColumns',
            'completionRate',
            'requiredCompletionRate',
            'upcomingDeadlines',
            'missingRequiredColumns'
        ));
    }
    
    /**
     * Kateqoriyaları filtrləyib qaytarır
     * 
     * @param int $schoolId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getFilteredCategories($schoolId)
    {
        // Kateqoriyaları əlaqələndirmələrə görə filterlə
        $categoryIds = CategoryAssignment::where(function($query) use ($schoolId) {
            $query->where('assigned_type', 'all')
                  ->orWhere(function($q) use ($schoolId) {
                      $q->where('assigned_type', 'school')
                        ->where('assigned_id', $schoolId);
                  });
        })->pluck('category_id')->unique();
        
        return Category::whereIn('id', $categoryIds)
            ->with([
                'columns' => function($query) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>', now());
                }, 
                'columns.dataValues' => function($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                }
            ])->get();
    }
}