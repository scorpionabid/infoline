<?php

namespace App\Http\Controllers\Web\Dashboard; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Entities\{
    Category,
    Column,
    DataValue,
    Region,
    Sector,
    School,
    User,
    SchoolData
};
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchoolDataExport;
use App\Services\Dashboard\StatisticsService;

class DashboardController extends Controller
{
    /**
     * Rola görə uyğun dashboard-a yönləndirir
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('super')) {
            return redirect()->route('dashboard.super-admin');
        } elseif ($user->hasRole('sector')) {
            return redirect()->route('dashboard.sector-admin');
        } elseif ($user->hasRole('school')) {
            return redirect()->route('dashboard.school-admin');
        }

        return redirect()->route('login');
    }

    /**
     * SuperAdmin dashboard
     */
    public function superAdmin()
    {
        $data = [
            'regionCount' => Region::count(),
            'sectorCount' => Sector::count(),
            'schoolCount' => School::count(),
            'userCount' => User::count()
        ];

        return view('pages.dashboard.super-admin', $data);
    }

    /**
     * Sector Admin dashboard
     */
    public function sectorAdmin()
    {
        $user = Auth::user();
        $sector = $user->sector;

        $data = [
            'sector' => $sector,
            'schoolCount' => School::where('sector_id', $sector->id)->count(),
            'regionCount' => Region::count(),
            'sectorCount' => Sector::count(), 
            'userCount' => User::count(),
            'sectors' => Sector::all(),
            'categories' => Category::all()
        ];

        // Məlumatları filtrlə
        $query = SchoolData::with(['school.sector', 'category']);

        if ($sectorId = request('sector_id')) {
            $query->whereHas('school', function($q) use ($sectorId) {
                $q->where('sector_id', $sectorId);
            });
        }

        if ($categoryId = request('category_id')) {
            $query->where('category_id', $categoryId); 
        }

        if ($status = request('status')) {
            $query->where('status', $status);
        }

        // Export əməliyyatı
        if (request('export')) {
            return Excel::download(
                new SchoolDataExport($query->get()),
                'school-data.xlsx'
            );
        }

        $data['schoolData'] = $query->paginate(15);

        return view('pages.dashboard.sector-admin.index', $data);
    }

    /**
     * School Admin dashboard
     */
    public function schoolAdmin()
    {
        $user = Auth::user();
        $school = $user->school;

        // Aktiv kateqoriyalar və sütunları əldə edirik
        $categories = Category::with(['columns' => function($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
        }])->get();

        // Məktəbin məlumatlarını əldə edirik
        $schoolId = $user->school_id;
        $dataValues = DataValue::where('school_id', $schoolId)->get();

        // Sütunların tam siyahısı
        $allColumns = $categories->flatMap(function($category) {
            return $category->columns;
        });

        // Toplam sütunlar
        $totalColumns = $allColumns->count();

        // Boş sütunların hesablanması
        $emptyColumns = $allColumns->filter(function($column) use ($dataValues) {
            return $dataValues->where('column_id', $column->id)->isEmpty();
        })->count();

        // Məcburi sütunlar
        $requiredColumns = $allColumns->filter(function($column) {
            return $column->is_required;
        });

        $filledRequiredColumns = $requiredColumns->filter(function($column) use ($dataValues) {
            return $dataValues->where('column_id', $column->id)->isNotEmpty();
        })->count();

        $totalRequiredColumns = $requiredColumns->count();

        // Tamamlanma faizi
        $completionRate = $totalColumns > 0 
            ? round(($totalColumns - $emptyColumns) / $totalColumns * 100, 2) 
            : 0;

        // Növbəti 30 gün ərzində olan vaxtın ələ indi ərzində olunan vaxtlar
        $upcomingDeadlines = Deadline::where('school_id', $schoolId)
            ->where('end_date', '>', now())
            ->where('end_date', '<=', now()->addDays(30)) // Növbəti 30 gün ərzində
            ->orderBy('end_date', 'asc')
            ->get();

        $statistics = [
            'completion_rate' => $completionRate,
            'empty_columns' => $emptyColumns,
            'total_columns' => $totalColumns,
            'required_columns' => $totalRequiredColumns,
            'filled_required_columns' => $filledRequiredColumns,
            'upcoming_deadlines' => $upcomingDeadlines
        ];

        $data = [
            'school' => $school,
            'categories' => $categories,
            'dataValues' => $dataValues,
            'statistics' => $statistics,
            'upcomingDeadlines' => $upcomingDeadlines
        ];

        return view('pages.dashboard.school-admin.index', $data);
    }
}