<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Domain\Entities\User;
use App\Domain\Entities\Role;
use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;
use App\Domain\Entities\School;
use App\Domain\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\User\StoreUserRequest;
use App\Http\Requests\Settings\User\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:super']);
    }

    /**
     * İstifadəçilərin siyahısı
     */
    public function index()
    {
        // Superadmin yalnız sektor və məktəb adminləri yarada bilər
        $user_types = [
            UserType::SECTOR_ADMIN->value => 'Sektor Admin',
            UserType::SCHOOL_ADMIN->value => 'Məktəb Admin'
        ];

        $users = User::with(['region', 'sector', 'school'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $regions = Region::orderBy('name')->get();
        $sectors = Sector::orderBy('name')->get();
        $schools = School::orderBy('name')->get();

        return view('pages.settings.personal.users.index', compact(
            'users',
            'user_types',
            'regions',
            'sectors',
            'schools'
        ));
    }

    /**
     * Yeni istifadəçi yaratma forması
     */
    public function create()
    {
        // Superadmin yalnız sektor və məktəb adminləri yarada bilər
        $user_types = [
            UserType::SECTOR_ADMIN->value => 'Sektor Admin',
            UserType::SCHOOL_ADMIN->value => 'Məktəb Admin'
        ];

        $regions = Region::orderBy('name')->get();
        $sectors = Sector::orderBy('name')->get();
        $schools = School::orderBy('name')->get();

        return view('pages.settings.personal.users.create', compact(
            'user_types',
            'regions',
            'sectors',
            'schools'
        ));
    }

    /**
     * Yeni istifadəçi yaratma
     */
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();

            // İstifadəçi yaradılması
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'utis_code' => $request->utis_code,
                'user_type' => $request->user_type,
                'region_id' => $request->region_id,
                'sector_id' => $request->sector_id,
                'school_id' => $request->school_id,
            ]);

            // Rolların təyin edilməsi
            if ($request->has('roles')) {
                $user->roles()->sync($request->roles);
            }

            DB::commit();

            return redirect()
                ->route('settings.users.index')
                ->with('success', 'İstifadəçi uğurla yaradıldı');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * İstifadəçi düzəliş forması
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $regions = Region::all();
        $sectors = Sector::all();
        $schools = School::all();

        return view('pages.settings.personal.users.edit', compact(
            'user', 'roles', 'regions', 'sectors', 'schools'
        ));
    }

    /**
     * İstifadəçi məlumatlarının yenilənməsi
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            // İstifadəçi məlumatlarının yenilənməsi
            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'utis_code' => $request->utis_code,
                'user_type' => $request->user_type,
                'region_id' => $request->region_id,
                'sector_id' => $request->sector_id,
                'school_id' => $request->school_id,
            ]);

            // Şifrə dəyişikliyi
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // Rolların yenilənməsi
            if ($request->has('roles')) {
                $user->roles()->sync($request->roles);
            }

            DB::commit();

            return redirect()
                ->route('settings.users.index')
                ->with('success', 'İstifadəçi məlumatları yeniləndi');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    /**
     * İstifadəçinin statusunun dəyişdirilməsi
     */
    public function updateStatus(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Super admin statusu dəyişdirilə bilməz');
        }

        $user->update([
            'is_active' => $request->status
        ]);

        return back()->with('success', 'İstifadəçi statusu yeniləndi');
    }

    /**
     * İstifadəçinin rollarının yenilənməsi
     */
    public function updateRoles(Request $request, User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Super admin rolları dəyişdirilə bilməz');
        }

        $user->roles()->sync($request->roles);

        return back()->with('success', 'İstifadəçi rolları yeniləndi');
    }

    /**
     * İstifadəçinin silinməsi
     */
    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'SuperAdmin silinə bilməz');
        }

        $user->delete();

        return back()->with('success', 'İstifadəçi silindi');
    }
}