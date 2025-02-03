<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\API\V1\BaseController;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function statistics(): JsonResponse
    {
        $stats = $this->dashboardService->getStatistics();
        return $this->sendResponse($stats, 'Statistika uğurla əldə edildi');
    }

    public function regionStatistics(): JsonResponse
    {
        $stats = $this->dashboardService->getRegionStatistics();
        return $this->sendResponse($stats, 'Region statistikası uğurla əldə edildi');
    }

    public function schoolStatistics(Request $request): JsonResponse
    {
        $stats = $this->dashboardService->getSchoolStatistics(
            $request->get('region_id'),
            $request->get('sector_id')
        );
        return $this->sendResponse($stats, 'Məktəb statistikası uğurla əldə edildi');
    }

    public function dataSubmissionStats(): JsonResponse
    {
        $stats = $this->dashboardService->getDataSubmissionStats();
        return $this->sendResponse($stats, 'Məlumat təqdimetmə statistikası uğurla əldə edildi');
    }
}