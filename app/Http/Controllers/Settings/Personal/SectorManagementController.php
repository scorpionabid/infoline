<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use App\Domain\Entities\{Sector, Region};
use App\Application\Services\SectorService;
use App\Http\Requests\Settings\Sector\{StoreSectorRequest, UpdateSectorRequest};
use App\Application\DTOs\SectorDTO;
use Illuminate\Support\Facades\{DB, Log};
use Illuminate\Http\Request;
use App\Domain\Entities\User;

class SectorManagementController extends Controller
{
    private SectorService $sectorService;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }

    public function index()
    {
        $regions = Region::with(['sectors' => function($query) {
            $query->withCount('schools')
                  ->with('admin');
        }])->orderBy('name')->get();

        return view('pages.settings.personal.sectors.index', compact('regions'));
    }

    public function create()
    {
        $regions = Region::orderBy('name')->get();
        return view('pages.settings.personal.sectors.create', compact('regions'));
    }

    public function store(StoreSectorRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $dto = new SectorDTO($request->validated());
            $sector = $this->sectorService->create($dto);
            
            DB::commit();
            
            return redirect()
                ->route('settings.personal.sectors.index')
                ->with('success', 'Sektor uğurla yaradıldı');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sector creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Sektor yaradılarkən xəta baş verdi']);
        }
    }

    public function edit(Sector $sector)
    {
        $regions = Region::orderBy('name')->get();
        $users = User::orderBy('first_name')
            ->orderBy('last_name')
            ->get();
        return view('pages.settings.personal.sectors.edit', compact('sector', 'regions', 'users'));
    }

    public function update(UpdateSectorRequest $request, Sector $sector)
    {
        try {
            DB::beginTransaction();
            
            $dto = new SectorDTO($request->validated());
            $sector = $this->sectorService->update($sector->id, $dto);
            
            DB::commit();
            
            return redirect()
                ->route('settings.personal.sectors.index')
                ->with('success', 'Sektor uğurla yeniləndi');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sector update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->withErrors(['error' => 'Sektor yenilənərkən xəta baş verdi']);
        }
    }

    public function destroy(Sector $sector)
    {
        try {
            DB::beginTransaction();
            
            if ($sector->schools()->count() > 0) {
                return back()->withErrors(['error' => 'Bu sektorun məktəbləri var. Əvvəlcə məktəbləri silin.']);
            }
            
            $sector->forceDelete();
            
            DB::commit();
            
            return redirect()
                ->route('settings.personal.sectors.index')
                ->with('success', 'Sektor uğurla silindi');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sector deletion error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withErrors(['error' => 'Sektor silinərkən xəta baş verdi']);
        }
    }

    public function assignAdmin(Request $request, Sector $sector)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            $sector->admin_id = $validatedData['user_id'];
            $sector->save();

            DB::commit();

            return redirect()
                ->route('settings.personal.sectors.edit', $sector)
                ->with('success', 'Sektor admini uğurla təyin edildi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Sector admin assignment error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'Sektor admini təyin edilərkən xəta baş verdi']);
        }
    }
}