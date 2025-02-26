<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use App\Domain\Entities\{School, Sector, Region};
use App\Http\Requests\Settings\School\{StoreSchoolRequest, UpdateSchoolRequest};
use App\Http\Requests\Settings\Personal\School\StoreSchoolAdminRequest;
use App\Domain\Entities\User;
use App\Events\School\{AdminAssigned, SchoolUpdated};
use App\Domain\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;

class SchoolController extends Controller
{
    public function __construct()
    {
        $this->middleware(['role:super']);
    }

    /**
     * Display a listing of schools.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $query = School::with(['sector.region', 'admin', 'admins']);
                
                if ($request->filled('sector_id')) {
                    $query->where('sector_id', $request->sector_id);
                }

                return DataTables::eloquent($query)
                    ->addColumn('region', fn($school) => $school->sector->region->name ?? '-')
                    ->addColumn('sector', fn($school) => $school->sector->name ?? '-')
                    ->addColumn('admin_name', fn($school) => $school->admin?->full_name ?? '-')
                    ->addColumn('admins_count', fn($school) => $school->admins->count())
                    ->addColumn('data_completion', fn($school) => $school->data_completion_percentage . '%')
                    ->addColumn('actions', function ($school) {
                        return view('pages.settings.personal.schools.partials.actions', compact('school'))->render();
                    })
                    ->rawColumns(['actions'])
                    ->toJson();
            } catch (\Exception $e) {
                Log::error('DataTables error', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'error' => true,
                    'message' => 'Məlumatları yükləyərkən xəta baş verdi.'
                ], 500);
            }
        }

        try {
            $regions = Region::orderBy('name')->get();
            $sectors = Sector::with('region')->orderBy('name')->get();
            $schoolTypes = config('enums.school_types');

            $query = School::with(['sector.region', 'admin', 'admins']);
            
            if ($request->filled('region_id')) {
                $query->whereHas('sector', function($q) use ($request) {
                    $q->where('region_id', $request->region_id);
                });
            }

            if ($request->filled('sector_id')) {
                $query->where('sector_id', $request->sector_id);
            }

            $schools = $query->orderBy('name')->paginate(10)->withQueryString();

            return view('pages.settings.personal.schools.index', compact('regions', 'sectors', 'schoolTypes', 'schools'));
        } catch (\Exception $e) {
            Log::error('Error loading schools page', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Səhifəni yükləyərkən xəta baş verdi.');
        }
    }

    /**
     * Store a newly created school.
     *
     * @param StoreSchoolRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Show the form for creating a new school.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $sectors = Sector::with('region')->orderBy('name')->get();
            $schoolTypes = config('enums.school_types');

            return view('pages.settings.personal.schools.create', compact('sectors', 'schoolTypes'));
        } catch (\Exception $e) {
            Log::error('Error loading school create page', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Səhifəni yükləyərkən xəta baş verdi.');
        }
    }

    /**
     * Store a newly created school.
     *
     * @param StoreSchoolRequest $request
     * @return \Illuminate\Http\JsonResponse
     */



    /**
     * Show form for creating a new admin
     *
     * @param School $school
     * @return \Illuminate\View\View
     */
    public function createAdmin(School $school)
    {
        $this->authorize('create', [User::class, $school]);
        return view('pages.settings.personal.schools.create-admin-form', compact('school'));
    }

    /**
     * Store a newly created admin
     *
     * @param StoreSchoolAdminRequest $request
     * @param School $school
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeAdmin(StoreSchoolAdminRequest $request, School $school)
    {
        try {
            DB::beginTransaction();

            // Check if school already has an admin
            if ($school->admin) {
                throw new \Exception('Bu məktəbin artıq admini var.');
            }

            // Generate UTIS code for school admin
            $utisCode = $request->utis_code;
            
            // Create new user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'user_type' => UserType::SCHOOL_ADMIN,
                'school_id' => $school->id,
                'utis_code' => $utisCode
            ]);

            // Assign school admin role from existing roles table
            $schoolAdminRole = Role::where('name', 'school')->firstOrFail();
            $user->roles()->attach($schoolAdminRole->id);

            // Assign user as school admin
            $user->school_id = $school->id;
            $user->save();

            $school->admin_id = $user->id;
            $school->save();

            // Fire admin assigned event
            event(new AdminAssigned($school, $user));

            DB::commit();

            Cache::tags(['schools'])->flush();

            return redirect()
                ->route('settings.personal.schools.index')
                ->with('success', 'Məktəb admini uğurla yaradıldı');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating school admin', [
                'school_id' => $school->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Admin yaradılarkən xəta baş verdi: ' . $e->getMessage()]);
        }
    }

    /**
     * Assign an existing user as admin to a school.
     *
     * @param Request $request
     * @param School $school
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignAdmin(Request $request, School $school)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            '_token' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->user_id);

            // Check if school already has an admin
            if ($school->admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu məktəbin artıq admini var'
                ], 422);
            }

            // Check if user is already an admin of another school
            if ($user->school_id && $user->school_id != $school->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu istifadəçi artıq başqa məktəbin adminidir'
                ], 422);
            }

            // Assign school admin role if not already assigned
            if (!$user->hasRole('school_admin')) {
                $role = Role::where('name', 'school_admin')->firstOrFail();
                $user->assignRole($role);
            }

            // Update user type
            $user->update(['user_type' => UserType::SCHOOL_ADMIN, 'school_id' => $school->id]);

            // Assign user as school admin
            $user->school_id = $school->id; 
            $user->save();

            // Fire admin assigned event
            event(new AdminAssigned($school, $user));

            DB::commit();

            Cache::tags(['schools'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Admin uğurla təyin edildi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning school admin', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Admin təyin edərkən xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Remove admin from a school.
     *
     * @param Request $request
     * @param School $school
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeAdmin(Request $request, School $school)
    {
        try {
            DB::beginTransaction();

            // Get current admin
            $admin = $school->admin;
            if (!$admin) {
                throw new \Exception('Məktəbin admini yoxdur');
            }

            // Remove admin from school
            $admin->school_id = null;
            $admin->save();

            // Remove admin_id from school
            $school->admin_id = null;
            $school->save();

            // Remove school_admin role if user is not admin of any other school
            if ($admin->schools()->count() === 0) {
                $admin->removeRole('school_admin');
            }
            DB::commit();

            Cache::tags(['schools'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Admin uğurla silindi'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing school admin', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Admin silərkən xəta baş verdi'
            ], 500);
        }
    }

    public function store(StoreSchoolRequest $request)
    {
        try {
            // Validate sector exists
            $sector = Sector::findOrFail($request->sector_id);

            // Validate school type
            if (!array_key_exists($request->type, config('enums.school_types'))) {
                throw new \InvalidArgumentException('Yanlış məktəb tipi');
            }

            DB::beginTransaction();

            // Create school
            $school = new School();
            $school->name = $request->name;
            $school->utis_code = $request->utis_code;
            $school->type = $request->type;
            $school->sector_id = $sector->id;
            $school->phone = $request->phone;
            $school->email = $request->email;
            $school->address = $request->address;
            $school->status = true;
            $school->save();

            DB::commit();

            // Load relationships for response
            $school->load(['sector.region']);

            return response()->json([
                'success' => true,
                'message' => 'Məktəb uğurla yaradıldı!',
                'data' => $school
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('School creation error - Sector not found', [
                'sector_id' => $request->sector_id,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Seçilmiş sektor tapılmadı.'
            ], 422);

        } catch (\InvalidArgumentException $e) {
            DB::rollBack();
            Log::error('School creation error - Invalid school type', [
                'type' => $request->type,
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('School creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Məktəb yaradılarkən xəta baş verdi!'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified school.
     *
     * @param School $school
     * @return \Illuminate\Http\Response
     */
    public function edit(School $school)
    {
        $sectors = Sector::with('region')->orderBy('name')->get();
        $users = User::orderBy('first_name')->orderBy('last_name')->get();
        return view('pages.settings.personal.schools.edit', compact('school', 'sectors', 'users'));
    }

    /**
     * Update the specified school.
     *
     * @param UpdateSchoolRequest $request
     * @param School $school
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSchoolRequest $request, School $school)
    {
        try {
            DB::beginTransaction();

            $school->update($request->validated());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Məktəb uğurla yeniləndi!',
                'data' => $school
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('School update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Məktəb yenilənərkən xəta baş verdi!'
            ], 500);
        }
    }



    /**
     * Show the school details.
     *
     * @param School $school
     * @return \Illuminate\Http\Response
     */
    public function show(School $school)
    {
        try {
            $school->load(['sector.region', 'admin', 'admins']);
            return view('pages.settings.personal.schools.show', compact('school'));
        } catch (\Exception $e) {
            Log::error('Error loading school details', [
                'school_id' => $school->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Məktəb məlumatlarını yükləyərkən xəta baş verdi.');
        }
    }

    /**
     * Get available admins for assignment.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableAdmins(Request $request)
    {
        try {
            $schoolAdminRole = Role::where('name', 'school')->firstOrFail();

            $query = User::role($schoolAdminRole)
                ->whereDoesntHave('schools')
                ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as text"));

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
            }

            $admins = $query->get();

            return response()->json([
                'results' => $admins
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting available admins', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Adminləri yükləyərkən xəta baş verdi.'
            ], 500);
        }
    }

    /**
     * Remove the specified school.
     *
     * @param School $school
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(School $school)
    {
        try {
            DB::beginTransaction();

            $school->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Məktəb uğurla silindi!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('School deletion error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Məktəb silinərkən xəta baş verdi!'
            ], 500);
        }
    }

}
