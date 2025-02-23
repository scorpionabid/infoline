<?php
// app/Http/Controllers/Web/Dashboard/SectorAdmin/DashboardController.php

namespace App\Http\Controllers\Web\Dashboard\SectorAdmin;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\SectorAdmin\StatisticsService;
use App\Services\Dashboard\SectorAdmin\SchoolManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $statisticsService;
    protected $schoolManagementService;

    public function __construct(
        StatisticsService $statisticsService,
        SchoolManagementService $schoolManagementService
    ) {
        $this->statisticsService = $statisticsService;
        $this->schoolManagementService = $schoolManagementService;
    }

    public function index()
    {
        $sectorId = Auth::user()->sector_id;

        // Sektor statistikası
        $statistics = $this->statisticsService->getSectorStatistics($sectorId);
        
        // Məktəblərin siyahısı və vəziyyətləri
        $schools = $this->schoolManagementService->getSchoolsWithStatus($sectorId);
        
        // Son tarixə yaxın məlumatlar
        $upcomingDeadlines = $this->statisticsService->getUpcomingDeadlines($sectorId);
        
        // Diqqət tələb edən məktəblər
        $criticalSchools = $this->schoolManagementService->getCriticalSchools($sectorId);

        return view('pages.dashboard.sector-admin.index', compact(
            'statistics',
            'schools',
            'upcomingDeadlines',
            'criticalSchools'
        ));
    }
}