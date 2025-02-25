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
        } else {
            return redirect()->route('dashboard.school-admin');
        }
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
     * Sektor admin dashboard-u
     */
    public function sectorAdmin()
    {
        $user = Auth::user();
    
        if (!$user->hasRole('sector')) {
            return redirect()->route('dashboard');
        }

        // Get user's sector and region
        $sector = $user->sector()->with(['schools.admins', 'region'])->first();
        $region = $user->region;

        if (!$sector || !$region) {
            return redirect()->route('dashboard')
                ->with('error', 'Sektor və ya region təyin edilməyib.');
        }

        // Statistika məlumatları
        $data = [
            'regionCount' => Region::count(),
            'sectorCount' => Sector::count(), 
            'schoolCount' => School::count(),
            'userCount' => User::count(),
            'sectors' => Sector::all(),
            'categories' => Category::all(),
            'sector' => $sector,
            'region' => $region
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

        return view('pages.dashboard.sector-admin', $data);
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