<?php

namespace App\Http\Controllers\Web\Dashboard; 

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
            return redirect()->route('dashboard.super-admin');
        } elseif ($user->isSectorAdmin()) {
            return redirect()->route('dashboard.sector-admin');
        } else {
            return redirect()->route('dashboard.school-admin');
        }
        $data = [
            'regionCount' => Region::count(),
            'sectorCount' => Sector::count(), 
            'schoolCount' => School::count(),
            'userCount' => User::count(),
        
        // Filter options
            'sectors' => Sector::all(),
            'categories' => Category::all()
        ];

    // Handle filters
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

    // Handle export
        if (request('export')) {
            return Excel::download(
                new SchoolDataExport($query->get()),
                'school-data.xlsx'
            );
        }

        $data['schoolData'] = $query->paginate(15);

        return view('pages.dashboard.super-admin', $data);
    }
    

    /**
     * SuperAdmin dashboard
     */
    public function superAdmin()
    {
        $data = [
            'regionCount' => \App\Domain\Entities\Region::count(),
            'sectorCount' => \App\Domain\Entities\Sector::count(),
            'schoolCount' => \App\Domain\Entities\School::count(),
            'userCount' => \App\Domain\Entities\User::count(),
        ];

        return view('pages.dashboard.super-admin', $data);
    }

    /**
     * SectorAdmin dashboard
     */
    public function sectorAdmin()
    {
        $user = Auth::user();
    
        if (!$user->isSectorAdmin()) {
            return redirect()->route('dashboard');
        }

    // Sector və əlaqəli məlumatları eager loading ilə gətiririk
        $sector = $user->sector()->with([
            'schools.admins',
            'region'
        ])->first();

        return view('pages.dashboard.sector-admin', compact('sector'));
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