<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Entities\{Sector, Region, School, User};
use App\Application\Services\{SectorService, UserService};
use App\Http\Requests\Settings\User\{StoreUserRequest, UpdateUserRequest};
use App\Http\Requests\Settings\Personal\{StoreSectorRequest, UpdateSectorRequest, StoreSectorAdminRequest};
use App\Application\DTOs\{UserDTO, SectorDTO};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;
use App\Exceptions\SectorAdminUpdateException;
use Illuminate\Http\JsonResponse;
use DataTables;

class SectorManagementController extends Controller
{
    private SectorService $sectorService;
    private UserService $userService;

    public function __construct(
        SectorService $sectorService,
        UserService $userService
    ) {
        $this->sectorService = $sectorService;
        $this->userService = $userService;
    }

    public function index()
    {
        $data = [
            'regions' => Region::all(),
            'totalSectors' => Sector::count(),
            'activeAdmins' => User::where('user_type', 'sectoradmin')->where('status', 'active')->count(),
            'totalSchools' => School::count()
        ];

        return view('pages.settings.personal.sectors.index', $data);
    }

    public function data(Request $request)
    {
        $query = Sector::with(['region', 'admin', 'schools'])
            ->when($request->region, function($q) use ($request) {
                return $q->where('region_id', $request->region);
            })
            ->when($request->admin_status, function($q) use ($request) {
                if ($request->admin_status === 'with_admin') {
                    return $q->whereNotNull('admin_id');
                } elseif ($request->admin_status === 'without_admin') {
                    return $q->whereNull('admin_id');
                }
            });

        return DataTables::of($query)
            ->addColumn('schools_count', function($sector) {
                return $sector->schools->count();
            })
            ->addColumn('actions', function($sector) {
                return view('pages.settings.personal.sectors.actions', compact('sector'));
            })
            ->rawColumns(['actions'])
            ->make(true);
    }

    public function create()
    {
        return view('pages.settings.personal.sectors.create', [
            'regions' => Region::all()
        ]);
    }

    public function store(StoreSectorRequest $request)
    {
        try {
            $sector = $this->sectorService->create($request->validated());
            return redirect()
                ->route('settings.personal.sectors.index')
                ->with('success', 'Sektor uğurla yaradıldı');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Sektor yaradılarkən xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function edit(Sector $sector)
    {
        return view('pages.settings.personal.sectors.edit', [
            'sector' => $sector,
            'regions' => Region::all()
        ]);
    }

    public function update(UpdateSectorRequest $request, Sector $sector)
    {
        try {
            $this->sectorService->update($sector->id, $request->validated());
            return redirect()
                ->route('settings.personal.sectors.index')
                ->with('success', 'Sektor uğurla yeniləndi');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Sektor yenilənərkən xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function destroy(Sector $sector)
    {
        try {
            $this->sectorService->delete($sector->id);
            return response()->json(['message' => 'Sektor uğurla silindi']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function assignAdmin(StoreSectorAdminRequest $request, Sector $sector)
    {
        try {
            $this->sectorService->assignAdmin($sector->id, $request->validated());
            return redirect()
                ->route('settings.personal.sectors.edit', $sector->id)
                ->with('success', 'Admin uğurla təyin edildi');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Admin təyin edilərkən xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        return $this->sectorService->exportToExcel($request->all());
    }
}