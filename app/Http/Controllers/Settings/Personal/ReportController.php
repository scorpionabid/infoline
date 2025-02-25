<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use App\Domain\Entities\Region;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:super']);
    }

    /**
     * Məktəblər üzrə hesabat
     */
    public function schools()
    {
        $schools = School::with(['sector.region', 'admin'])
            ->select([
                'schools.*',
                DB::raw('(SELECT COUNT(*) FROM school_data WHERE school_data.school_id = schools.id) as data_count')
            ])
            ->withCount('admins')
            ->get();

        return view('pages.settings.personal.reports.schools', compact('schools'));
    }

    /**
     * Sektorlar üzrə hesabat
     */
    public function sectors()
    {
        $sectors = Sector::with('region')
            ->withCount('schools')
            ->select([
                'sectors.*',
                DB::raw('(SELECT COUNT(*) FROM schools WHERE schools.sector_id = sectors.id AND schools.status = 1) as active_schools_count'),
                DB::raw('(SELECT COUNT(*) FROM users WHERE users.sector_id = sectors.id) as users_count')
            ])
            ->get();

        return view('pages.settings.personal.reports.sectors', compact('sectors'));
    }

    /**
     * Regionlar üzrə hesabat
     */
    public function regions()
    {
        $regions = Region::withCount(['sectors', 'schools'])
            ->select([
                'regions.*',
                DB::raw('(SELECT COUNT(*) FROM schools 
                         INNER JOIN sectors ON schools.sector_id = sectors.id 
                         WHERE sectors.region_id = regions.id AND schools.status = 1) 
                         as active_schools_count'),
                DB::raw('(SELECT COUNT(*) FROM users 
                         INNER JOIN sectors ON users.sector_id = sectors.id 
                         WHERE sectors.region_id = regions.id) 
                         as users_count')
            ])
            ->get();

        return view('pages.settings.personal.reports.regions', compact('regions'));
    }

    /**
     * Xüsusi hesabat
     */
    public function custom(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'type' => 'required|in:schools,sectors,regions',
            'status' => 'nullable|boolean',
            'region_id' => 'nullable|exists:regions,id',
            'sector_id' => 'nullable|exists:sectors,id'
        ]);

        $query = match($validated['type']) {
            'schools' => $this->getSchoolsQuery($validated),
            'sectors' => $this->getSectorsQuery($validated),
            'regions' => $this->getRegionsQuery($validated)
        };

        $data = $query->get();

        return response()->json([
            'data' => $data,
            'type' => $validated['type']
        ]);
    }

    /**
     * Məktəblər üzrə sorğu
     */
    private function getSchoolsQuery(array $filters)
    {
        $query = School::with(['sector.region', 'admin'])
            ->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['region_id'])) {
            $query->whereHas('sector', function($q) use ($filters) {
                $q->where('region_id', $filters['region_id']);
            });
        }

        if (isset($filters['sector_id'])) {
            $query->where('sector_id', $filters['sector_id']);
        }

        return $query->select([
            'schools.*',
            DB::raw('(SELECT COUNT(*) FROM school_data WHERE school_data.school_id = schools.id) as data_count')
        ])->withCount('admins');
    }

    /**
     * Sektorlar üzrə sorğu
     */
    private function getSectorsQuery(array $filters)
    {
        $query = Sector::with('region')
            ->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);

        if (isset($filters['region_id'])) {
            $query->where('region_id', $filters['region_id']);
        }

        return $query->withCount('schools')
            ->select([
                'sectors.*',
                DB::raw('(SELECT COUNT(*) FROM schools WHERE schools.sector_id = sectors.id AND schools.status = 1) as active_schools_count'),
                DB::raw('(SELECT COUNT(*) FROM users WHERE users.sector_id = sectors.id) as users_count')
            ]);
    }

    /**
     * Regionlar üzrə sorğu
     */
    private function getRegionsQuery(array $filters)
    {
        return Region::whereBetween('created_at', [$filters['start_date'], $filters['end_date']])
            ->withCount(['sectors', 'schools'])
            ->select([
                'regions.*',
                DB::raw('(SELECT COUNT(*) FROM schools 
                         INNER JOIN sectors ON schools.sector_id = sectors.id 
                         WHERE sectors.region_id = regions.id AND schools.status = 1) 
                         as active_schools_count'),
                DB::raw('(SELECT COUNT(*) FROM users 
                         INNER JOIN sectors ON users.sector_id = sectors.id 
                         WHERE sectors.region_id = regions.id) 
                         as users_count')
            ]);
    }
}