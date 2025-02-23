<?php

namespace App\Http\Controllers\Web\Dashboard\SchoolAdmin;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\SchoolAdmin\StatisticsService;
use App\Services\Dashboard\SchoolAdmin\DataEntryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $statisticsService;
    protected $dataEntryService;

    public function __construct(
        StatisticsService $statisticsService,
        DataEntryService $dataEntryService
    ) {
        $this->statisticsService = $statisticsService;
        $this->dataEntryService = $dataEntryService;
    }

    public function index()
    {
        $schoolId = Auth::user()->school_id;

        // Əsas statistika məlumatları
        $statistics = $this->statisticsService->getSchoolStatistics($schoolId);
        
        // Kateqoriyalar və onların sütunları
        $categories = $this->dataEntryService->getCategoriesWithColumns($schoolId);
        
        // Doldurulmamış məcburi sütunlar
        $emptyRequiredColumns = $this->dataEntryService->getEmptyRequiredColumns($schoolId);
        
        // Son tarixə yaxın sütunlar (3 gün)
        $upcomingDeadlines = $this->dataEntryService->getUpcomingDeadlines($schoolId, 3);
        
        // Yeni əlavə edilmiş sütunlar (son 7 gün)
        $newColumns = $this->dataEntryService->getNewlyAddedColumns($schoolId, 7);

        return view('pages.dashboard.school-admin.index', compact(
            'statistics',
            'categories',
            'emptyRequiredColumns',
            'upcomingDeadlines',
            'newColumns'
        ));
    }
}