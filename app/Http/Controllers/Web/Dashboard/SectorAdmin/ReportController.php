<?php
// app/Http/Controllers/Web/Dashboard/SectorAdmin/ReportController.php

namespace App\Http\Controllers\Web\Dashboard\SectorAdmin;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\SectorAdmin\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index()
    {
        $sectorId = Auth::user()->sector_id;

        // Ümumi statistika
        $statistics = $this->reportService->getGeneralStatistics($sectorId);
        
        // Məktəblərin müqayisəli analizi
        $schoolsComparison = $this->reportService->getSchoolsComparison($sectorId);
        
        // Kateqoriyalar üzrə analiz
        $categoryAnalysis = $this->reportService->getCategoryAnalysis($sectorId);
        
        // Son 12 ay üzrə trend
        $monthlyTrend = $this->reportService->getMonthlyTrend($sectorId);

        return view('pages.dashboard.sector-admin.reports.index', compact(
            'statistics',
            'schoolsComparison',
            'categoryAnalysis',
            'monthlyTrend'
        ));
    }

    public function exportExcel(Request $request)
    {
        $sectorId = Auth::user()->sector_id;
        $filters = $request->only(['start_date', 'end_date', 'school_id', 'category_id']);
        
        return $this->reportService->generateExcelReport($sectorId, $filters);
    }

    public function schoolReport($schoolId)
    {
        $report = $this->reportService->getSchoolDetailedReport($schoolId);
        
        return view('pages.dashboard.sector-admin.reports.school', compact('report'));
    }
}