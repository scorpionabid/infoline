<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use App\Domain\Entities\Region;
use App\Domain\Entities\User;
use App\Domain\Entities\Sector;
use App\Domain\Entities\School;
use App\Services\RegionService;
use App\Http\Requests\Settings\Region\StoreRegionRequest;
use App\Http\Requests\Settings\Region\UpdateRegionRequest;
use App\Http\Requests\Settings\Region\StoreRegionAdminRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use App\Domain\Enums\UserType;

class RegionManagementController extends Controller
{
    protected $regionService;

    public function __construct(RegionService $regionService)
    {
        $this->middleware(['role:super']);
        $this->regionService = $regionService;
    }

    public function index()
    {
        $regions = Region::withTrashed()
            ->withCount(['sectors', 'schools'])
            ->orderBy('name')
            ->get();

        return view('pages.settings.personal.regions.index', compact('regions'));
    }

    public function statistics()
    {
        try {
            $stats = [
                'regions_count' => Region::count(),
                'sectors_count' => Sector::count(),
                'schools_count' => School::count()
            ];

            return response()->json($stats);
        } catch (\Exception $e) {
            Log::error('Error fetching statistics', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Statistika məlumatları əldə edilərkən xəta baş verdi'
            ], 500);
        }
    }

    public function data()
    {
        try {
            Log::info('Fetching regions data');
            
            $regions = Region::withCount(['sectors', 'schools'])
                ->with(['admin' => function($query) {
                    $query->where('user_type', UserType::SECTOR_ADMIN);
                }])
                ->select('regions.*')
                ->get();

            return DataTables::of($regions)
                ->addColumn('admin', function ($region) {
                    return $region->admin ? $region->admin->full_name : 'Təyin edilməyib';
                })
                ->addColumn('actions', function ($region) {
                    return view('pages.settings.personal.regions.actions', compact('region'));
                })
                ->rawColumns(['actions'])
                ->make(true);

        } catch (\Exception $e) {
            Log::error('Error fetching regions data', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Məlumatları əldə edərkən xəta baş verdi'
            ], 500);
        }
    }

    public function create()
    {
        return view('pages.settings.personal.regions.create');
    }

    public function store(StoreRegionRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            
            // Region adının təkrarlanmamasını yoxla (silinmiş regionlar da daxil)
            $existingRegion = Region::withTrashed()
                ->where('name', $data['name'])
                ->first();
                
            if ($existingRegion) {
                return back()
                    ->withInput()
                    ->withErrors(['name' => 'Bu region adı artıq mövcuddur']);
            }
            
            // Telefon nömrəsinin təkrarlanmamasını yoxla (silinmiş regionlar da daxil)
            if (!empty($data['phone'])) {
                $existingPhone = Region::withTrashed()
                    ->where('phone', $data['phone'])
                    ->first();
                    
                if ($existingPhone) {
                    return back()
                        ->withInput()
                        ->withErrors(['phone' => 'Bu telefon nömrəsi artıq mövcuddur']);
                }
            }
            
            $region = Region::create($data);
            
            DB::commit();
            
            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region uğurla əlavə edildi');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Region yaradılarkən xəta baş verdi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['system' => 'Sistem xətası baş verdi']);
        }
    }

    public function edit(Region $region)
    {
        return view('pages.settings.personal.regions.edit', compact('region'));
    }

    public function update(UpdateRegionRequest $request, Region $region)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            
            // Region adının təkrarlanmamasını yoxla (özündən başqa)
            $existingRegion = Region::where('name', $data['name'])
                ->where('id', '!=', $region->id)
                ->first();
                
            if ($existingRegion) {
                return back()
                    ->withInput()
                    ->withErrors(['name' => 'Bu region adı artıq mövcuddur']);
            }
            
            // Telefon nömrəsinin təkrarlanmamasını yoxla
            if (!empty($data['phone'])) {
                $existingPhone = Region::where('phone', $data['phone'])
                    ->where('id', '!=', $region->id)
                    ->first();
                    
                if ($existingPhone) {
                    return back()
                        ->withInput()
                        ->withErrors(['phone' => 'Bu telefon nömrəsi artıq mövcuddur']);
                }
            }
            
            $region->fill($data);
            
            if ($region->isDirty('name')) {
                // Yeni kod generasiya et
                $code = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $data['name']));
                $count = 1;
                $newCode = $code;
                
                while (Region::where('code', $newCode)
                    ->where('id', '!=', $region->id)
                    ->exists()) {
                    $newCode = $code . $count;
                    $count++;
                }
                
                $region->code = $newCode;
            }
            
            $region->save();
            
            DB::commit();
            
            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region uğurla yeniləndi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Region yenilənərkən xəta baş verdi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['system' => 'Sistem xətası baş verdi']);
        }
    }

    public function destroy(Region $region)
    {
        try {
            DB::beginTransaction();
            
            if ($region->sectors()->count() > 0) {
                return back()->withErrors(['error' => 'Bu regionun sektorları var. Əvvəlcə sektorları silin.']);
            }
            
            $region->forceDelete(); // Birdəfəlik silmək üçün
            
            DB::commit();
            
            Log::info('Region permanently deleted', ['region' => $region]);
            
            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region uğurla silindi');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Region silinərkən xəta baş verdi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Sistem xətası baş verdi']);
        }
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();
            
            $region = Region::withTrashed()->findOrFail($id);
            
            if (!$region->trashed()) {
                return back()->withErrors(['error' => 'Bu region artıq aktiv vəziyyətdədir']);
            }
            
            // Region adının təkrarlanmamasını yoxla
            $existingRegion = Region::where('name', $region->name)
                ->where('id', '!=', $region->id)
                ->whereNull('deleted_at')
                ->first();
                
            if ($existingRegion) {
                return back()->withErrors(['error' => 'Bu region adı artıq mövcuddur. Regionu bərpa etmək üçün əvvəlcə adını dəyişin.']);
            }
            
            // Telefon nömrəsinin təkrarlanmamasını yoxla
            if ($region->phone) {
                $existingPhone = Region::where('phone', $region->phone)
                    ->where('id', '!=', $region->id)
                    ->whereNull('deleted_at')
                    ->first();
                    
                if ($existingPhone) {
                    return back()->withErrors(['error' => 'Bu telefon nömrəsi artıq mövcuddur. Regionu bərpa etmək üçün əvvəlcə telefon nömrəsini dəyişin.']);
                }
            }
            
            $region->restore();
            
            DB::commit();
            
            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region uğurla bərpa edildi');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Region bərpa edilərkən xəta baş verdi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Sistem xətası baş verdi']);
        }
    }

    public function forceDelete($id)
    {
        try {
            DB::beginTransaction();
            
            $region = Region::withTrashed()->findOrFail($id);
            
            if (!$region->trashed()) {
                return back()->withErrors(['error' => 'Aktiv regionu tam silmək mümkün deyil. Əvvəlcə regionu silin.']);
            }
            
            // Əlaqəli məlumatları yoxla
            $sectorsCount = $region->sectors()->count();
            $schoolsCount = $region->schools()->count();
            
            if ($sectorsCount > 0 || $schoolsCount > 0) {
                return back()->withErrors(['error' => 'Bu regionun əlaqəli məlumatları var. Tam silmək üçün əvvəlcə əlaqəli məlumatları silin.']);
            }
            
            $region->forceDelete();
            
            DB::commit();
            
            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region birdəfəlik silindi');
            
            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region birdəfəlik silindi');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Region birdəfəlik silinərkən xəta baş verdi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Sistem xətası baş verdi']);
        }
    }

    public function assignAdmin(StoreRegionAdminRequest $request, Region $region)
    {
        try {
            $result = $this->regionService->updateAdmin($region, $request->validated());
            
            if (!$result['success']) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => $result['message']]);
            }
            
            Log::info('Admin assigned to region', ['region' => $region, 'result' => $result]);
            
            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region adminı uğurla təyin edildi');
        } catch (\Exception $e) {
            Log::error('Region adminı təyin edilərkən xəta baş verdi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Sistem xətası baş verdi']);
        }
    }

    public function removeAdmin(Region $region)
    {
        try {
            $result = $this->regionService->removeAdmin($region);
            
            if (!$result['success']) {
                return back()->withErrors(['error' => $result['message']]);
            }
            
            Log::info('Admin removed from region', ['region' => $region, 'result' => $result]);
            
            return redirect()
                ->route('settings.personal.regions.index')
                ->with('success', 'Region adminı uğurla silindi');
        } catch (\Exception $e) {
            Log::error('Region adminı silinərkən xəta baş verdi', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Sistem xətası baş verdi']);
        }
    }
}