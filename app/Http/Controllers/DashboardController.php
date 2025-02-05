<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Entities\Category;
use App\Domain\Entities\Column;
use App\Domain\Entities\DataValue;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Rola görə uyğun dashboard-a yönləndirir
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isSuperAdmin()) {
            return $this->superAdmin();
        } elseif ($user->isSectorAdmin()) {
            return $this->sectorAdmin();
        } else {
            return $this->schoolAdmin();
        }
    }

    /**
     * SuperAdmin dashboard
     */
    public function superAdmin()
    {
        return view('pages.dashboard.super-admin');
    }

    /**
     * SectorAdmin dashboard
     */
    public function sectorAdmin()
    {
        return view('pages.dashboard.sector-admin');
    }

    /**
     * SchoolAdmin dashboard
     */
    public function schoolAdmin()
    {
        // Aktiv kateqoriyalar və sütunları əldə edirik
        $categories = Category::with(['columns' => function($query) {
            $query->whereNull('end_date')
                  ->orWhere('end_date', '>', now());
        }])->get();

        // Məktəbin məlumatlarını əldə edirik
        $schoolId = Auth::user()->school_id;
        $dataValues = DataValue::where('school_id', $schoolId)->get();

        return view('pages.dashboard.school-admin', [
            'categories' => $categories,
            'dataValues' => $dataValues
        ]);
    }
}