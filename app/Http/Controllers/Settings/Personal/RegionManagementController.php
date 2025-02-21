<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use App\Domain\Entities\Region;
use App\Domain\Entities\User;
use App\Services\RegionService;
use App\Http\Requests\Settings\Region\StoreRegionRequest;
use App\Http\Requests\Settings\Region\UpdateRegionRequest;
use App\Http\Requests\Settings\Region\StoreRegionAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RegionManagementController extends Controller
{
    protected $regionService;

    public function __construct(RegionService $regionService)
    {
        $this->regionService = $regionService;
    }

    public function index()
    {
        return view('pages.settings.personal.regions.index');
    }

    public function data()
    {
        $regions = Region::with(['admin'])
            ->withCount(['sectors', 'schools'])
            ->select('regions.*');

        return DataTables::of($regions)
            ->addColumn('admin', function ($region) {
                return $region->admin;
            })
            ->addColumn('actions', function ($region) {
                return view('pages.settings.personal.regions.actions', compact('region'));
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        return view('pages.settings.personal.regions.create');
    }

    public function store(StoreRegionRequest $request)
    {
        try {
            $region = Region::create($request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Region uğurla yaradıldı',
                    'region' => $region
                ]);
            }

            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region uğurla yaradıldı');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xəta baş verdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit(Region $region)
    {
        $statistics = $this->regionService->getStatistics($region);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'region' => $region,
                'statistics' => $statistics
            ]);
        }

        return view('pages.settings.personal.regions.edit', compact('region', 'statistics'));
    }

    public function update(UpdateRegionRequest $request, Region $region)
    {
        try {
            $region->update($request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Region məlumatları yeniləndi',
                    'region' => $region
                ]);
            }

            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region məlumatları yeniləndi');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xəta baş verdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Region $region)
    {
        try {
            if ($region->sectors()->count() > 0) {
                throw new \Exception('Bu regionda sektorlar var. Əvvəlcə sektorları silin.');
            }

            $region->delete();

            return response()->json([
                'success' => true,
                'message' => 'Region uğurla silindi'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function assignAdmin(StoreRegionAdminRequest $request, Region $region)
    {
        $result = $this->regionService->updateAdmin($region, $request->validated());
        
        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function removeAdmin(Region $region)
    {
        $result = $this->regionService->removeAdmin($region);
        
        return response()->json($result, $result['success'] ? 200 : 422);
    }
}