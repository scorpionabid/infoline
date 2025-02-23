<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use App\Domain\Entities\{School, Sector, Region};
use App\Http\Requests\Settings\School\{StoreSchoolRequest, UpdateSchoolRequest};
use App\Domain\Entities\User;
use App\Events\School\{AdminAssigned, SchoolUpdated};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log};
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;

class SchoolController extends Controller
{
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
            $sectors = Sector::with('region')->orderBy('name')->get();
            $schoolTypes = config('enums.school_types');

            return view('pages.settings.personal.schools.index', compact('sectors', 'schoolTypes'));
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
            $schoolAdminRole = Role::where('name', 'school_admin')->firstOrFail();

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
     * Assign admin to school.
     *
     * @param Request $request
     * @param School $school
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignAdmin(Request $request, School $school)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $admin = User::findOrFail($request->admin_id);
            $schoolAdminRole = Role::where('name', 'school_admin')->firstOrFail();

            // Check if user has school_admin role
            if (!$admin->hasRole($schoolAdminRole)) {
                throw new \InvalidArgumentException('Seçilmiş istifadəçi məktəb admini deyil.');
            }

            // Check if admin is already assigned to another school
            if ($admin->schools()->exists()) {
                throw new \InvalidArgumentException('Seçilmiş admin artıq başqa məktəbə təyin edilib.');
            }

            // Assign admin to school
            $school->admins()->attach($admin->id);

            // If this is the first admin, set as primary admin
            if (!$school->admin_id) {
                $school->admin_id = $admin->id;
                $school->save();
            }

            DB::commit();

            event(new AdminAssigned($school, $admin));

            return response()->json([
                'success' => true,
                'message' => 'Admin uğurla təyin edildi.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error assigning admin to school', [
                'school_id' => $school->id,
                'admin_id' => $request->admin_id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => $e instanceof \InvalidArgumentException ? $e->getMessage() : 'Admin təyin edərkən xəta baş verdi.'
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





            // Assign admin to school
            $school->admins()->attach($admin->id);

            // If this is the first admin, set as primary admin
            if (!$school->admin_id) {
                $school->admin_id = $admin->id;
                $school->save();
            }

            DB::commit();

            event(new AdminAssigned($school, $admin));

            return response()->json([
                'success' => true,
                'message' => 'Admin uğurla təyin edildi.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error assigning admin to school', [
                'school_id' => $school->id,
                'admin_id' => $request->admin_id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => $e instanceof \InvalidArgumentException ? $e->getMessage() : 'Admin təyin edərkən xəta baş verdi.'
            ], 500);
        }
    }
}
