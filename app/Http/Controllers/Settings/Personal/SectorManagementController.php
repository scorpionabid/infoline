<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use App\Domain\Entities\{Sector, Region, Role};
use App\Application\Services\SectorService;
use App\Http\Requests\Settings\Sector\{StoreSectorRequest, UpdateSectorRequest};
use App\Application\DTOs\SectorDTO;
use Illuminate\Support\Facades\{DB, Log, Validator};
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Domain\Entities\User;
use App\Domain\Enums\UserType;

class SectorManagementController extends Controller
{
    private SectorService $sectorService;

    public function __construct(SectorService $sectorService)
    {
        $this->middleware(['role:super']);
        $this->sectorService = $sectorService;
    }

    public function index()
    {
        $regions = Region::with(['sectors' => function($query) {
            $query->withCount('schools')
                  ->with(['admin', 'user']);
        }])->orderBy('name')->get();

        $users = User::whereDoesntHave('roles', function($query) {
            $query->where('name', 'super');
        })->orderBy('first_name')->orderBy('last_name')->get();

        return view('pages.settings.personal.sectors.index', compact('regions', 'users'));
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
            return back()->withErrors(['error' => 'Sektor yaradılarkən xəta baş verdi']);
        }
    }

    public function edit(Sector $sector)
    {
        $regions = Region::orderBy('name')->get();
        
        // Get users who are not superadmins for the admin selection dropdown
        $users = User::whereDoesntHave('roles', function($query) {
            $query->where('name', 'super');
        })->orderBy('first_name')->orderBy('last_name')->get();
        
        return view('pages.settings.personal.sectors.edit', compact('sector', 'regions', 'users'));
    }

    public function update(UpdateSectorRequest $request, Sector $sector)
    {
        try {
            DB::beginTransaction();
            
            $dto = new SectorDTO($request->validated());
            $this->sectorService->update($sector, $dto);
            
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
            return back()->withErrors(['error' => 'Sektor yenilənərkən xəta baş verdi']);
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
            return redirect()->route('settings.personal.sectors.index')
                ->with('success', 'Sektor uğurla silindi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Sektor silinərkən xəta baş verdi']);
        }
    }

    /**
     * Assign an admin to a sector
     *
     * @param Sector $sector
     * @param Request $request
     * @return RedirectResponse
     */
    public function assignAdmin(Sector $sector, Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);

            DB::beginTransaction();

            // Get the user
            $user = User::findOrFail($request->user_id);

            // Check if user is already an admin somewhere
            if ($user->sector()->exists()) {
                return back()->with('error', 'Bu istifadəçi artıq başqa bir sektorun adminidir.');
            }

            // Check if sector already has an admin
            if ($sector->admin()->exists()) {
                return back()->with('error', 'Bu sektorun artıq admini var.');
            }

            // Assign the user as sector admin
            $sector->admin()->associate($user);
            $sector->save();

            // Assign sector admin role to user
            $role = Role::where('name', 'sector')->firstOrFail();
            $user->roles()->syncWithoutDetaching([$role->id]);

            DB::commit();

            return back()->with('success', 'Sektor admini uğurla təyin edildi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove admin from a sector
     *
     * @param Sector $sector
     * @return RedirectResponse
     */
    public function removeAdmin(Sector $sector)
    {
        try {
            DB::beginTransaction();
            
            // Check if sector has an admin
            if (!$sector->admin()->exists()) {
                return back()->with('error', 'Bu sektorun admini yoxdur.');
            }
            
            // Get the admin user
            $admin = $sector->admin;
            
            // Remove sector admin role
            $role = Role::where('name', 'sector')->firstOrFail();
            $admin->roles()->detach($role->id);
            
            // Remove admin from sector
            $sector->admin()->dissociate();
            $sector->save();
            
            DB::commit();
            return back()->with('success', 'Admin uğurla silindi.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * Sektor admini yaratma formasını göstərir
     * 
     * @param Sector $sector
     * @return \Illuminate\View\View
     */
    public function createAdminForm(Sector $sector)
    {
        try {
            // Sektorun mövcudluğunu yoxlayaq
            if (!$sector || !$sector->exists) {
                throw new \Exception('Sektor tapılmadı.');
            }

            // Sektorun admininin olub-olmadığını yoxlayaq
            if ($sector->admin) {
                throw new \Exception('Bu sektorun artıq admini var.');
            }

            return view('pages.settings.personal.sectors.create-admin-form', compact('sector'));

        } catch (\Exception $e) {
            return redirect()
                ->route('settings.personal.sectors.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Sektor adminini yaradır
     * 
     * @param Request $request
     * @param Sector $sector
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAdmin(Request $request, Sector $sector)
    {
        try {
            // Sektorun mövcudluğunu yoxlayaq
            if (!$sector || !$sector->exists) {
                throw new \Exception('Sektor tapılmadı.');
            }

            // Sektorun admininin olub-olmadığını yoxlayaq
            if ($sector->admin) {
                throw new \Exception('Bu sektorun artıq admini var.');
            }

            // Log-a yazaq
            Log::info('Admin creation request for sector', [
                'sector_id' => $sector->id,
                'request_data' => $request->except(['password', 'password_confirmation'])
            ]);

            // Validasiya
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            DB::beginTransaction();

            try {
                // İstifadəçi yarat
                $user = User::create([
                    'first_name' => $validated['first_name'],
                    'last_name' => $validated['last_name'],
                    'email' => $validated['email'],
                    'password' => bcrypt($validated['password']),
                    'utis_code' => 'UTI' . time() . rand(1000, 9999), // Unikal UTIS kod generasiya edirik
                    'username' => strtolower($validated['first_name'] . '.' . $validated['last_name']), // Username yaradırıq
                    'user_type' => UserType::SECTOR_ADMIN->value // User tipini təyin edirik
                ]);

                // Role-u təyin et
                $role = Role::where('name', 'sector')->firstOrFail();
                $user->roles()->attach($role->id);

                // İstifadəçini sektora admin olaraq təyin et
                $sector->admin()->associate($user);
                $sector->save();

                DB::commit();

                Log::info('Admin created successfully', [
                    'sector_id' => $sector->id,
                    'user_id' => $user->id
                ]);

                return redirect()
                    ->route('settings.personal.sectors.index')
                    ->with('success', 'Admin uğurla yaradıldı');

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Admin creation failed', [
                'sector_id' => $sector->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Xəta: ' . $e->getMessage()]);
        }
    }
}
