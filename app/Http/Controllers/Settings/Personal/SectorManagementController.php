<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Entities\Sector;
use App\Domain\Entities\Region;
use App\Domain\Entities\School;
use App\Domain\Entities\User;
use Illuminate\Support\Facades\Log;
use App\Application\Services\SectorService;
use App\Application\Services\UserService;
use App\Http\Requests\Settings\User\StoreUserRequest;
use App\Http\Requests\Settings\User\UpdateUserRequest;
use App\Application\DTOs\UserDTO;
use App\Http\Requests\API\V1\Sector\StoreSectorAdminRequest;
use App\Application\DTOs\SectorDTO;

class SectorManagementController extends Controller
{
    public function index()
    {
        $sectors = Sector::with(['region', 'admin'])->withCount('schools')->paginate(20);
        $regions = Region::all(); // Modal üçün
        $users = User::whereIn('user_type', ['sectoradmin', 'super_admin'])->get(); // Admin seçimi üçün
    
        return view('pages.settings.personal.sectors.index', compact('sectors', 'regions', 'users'));
    }

    public function create()
    {
        $regions = Region::all();
        return view('pages.settings.personal.sectors.create', compact('regions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:sectors|max:255',
            'region_id' => 'required|exists:regions,id',
            'phone' => 'nullable|string'
        ]);

        $sector = Sector::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sektor uğurla yaradıldı',
                'sector' => $sector
            ]);
        }

        return redirect()
            ->route('settings.personal.sectors.index')
            ->with('success', 'Sektor uğurla yaradıldı');
    }

    public function edit(Sector $sector)
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'sector' => [
                    'id' => $sector->id,
                    'name' => $sector->name,
                    'phone' => $sector->phone,
                    'region_id' => $sector->region_id
                ]
            ]);
        }
    return view('settings.sectors.edit', compact('sector'));

    }

    public function update(Request $request, Sector $sector)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:sectors,name,' . $sector->id,
            'region_id' => 'required|exists:regions,id',
            'phone' => 'nullable|string'
        ]);

        $sector->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sektor məlumatları yeniləndi',
                'sector' => $sector
            ]);
        }

        return redirect()
            ->route('settings.personal.sectors.index')
            ->with('success', 'Sektor məlumatları yeniləndi');
    }

    public function destroy(Sector $sector)
    {
        if ($sector->schools()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Bu sektorda məktəblər var. Əvvəlcə məktəbləri silin.'
            ], 422);
        }

    $sector->delete();

    return response()->json([
        'success' => true,
        'message' => 'Sektor silindi'
    ]);
    }

    public function addSchool(Request $request, Sector $sector)
    {
        $validated = $request->validate([
            'school_id' => 'required|exists:schools,id'
        ]);

        // Əgər əlaqə modeli varsa
        $sector->schools()->attach($validated['school_id']);

        return back()->with('success', 'Məktəb sektora əlavə edildi');
    }

    public function assignAdmin(StoreSectorAdminRequest $request, int $sectorId)
    {
        try {
            $validated = $request->validated();
            $userDTO = UserDTO::fromRequest($request);
            $sector = $this->sectorService->updateSectorAdmin($sectorId, $userDTO);
        
            return response()->json([
                'message' => 'Sektor admini uğurla təyin edildi',
                'sector' => $sector
            ]);
        } catch (\Exception $e) {
            Log::error('Sektor admin təyinatı xətası: ' . $e->getMessage());
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function __construct(
        private SectorService $sectorService,
        private UserService $userService
    ) {}
}