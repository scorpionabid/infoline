<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use App\Services\School\SchoolService;
use App\Services\Admin\AdminAssignmentService;
use App\Domain\Entities\{School, Sector, Region, User, Category};
use App\Domain\Enums\{UserType, SchoolStatus};
use App\Http\Requests\Settings\School\{
    StoreSchoolRequest,
    UpdateSchoolRequest,
    AssignAdminRequest,
    BulkActionRequest,
    UpdateSchoolDataRequest,
    CreateSchoolAdminRequest
};
use App\Exceptions\{SchoolException, AdminAssignmentException};
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\{Log, DB, Cache};
use Illuminate\View\View;
use Illuminate\Database\Eloquent\Builder;
use App\Domain\Entities\Role;

class SchoolManagementController extends Controller
{
    protected SchoolService $schoolService;
    protected AdminAssignmentService $adminService;
    private const CACHE_TTL = 3600; // 1 hour
    private const PAGINATION_LIMIT = 25;

    public function __construct(
        SchoolService $schoolService,
        AdminAssignmentService $adminService
    ) {
        $this->schoolService = $schoolService;
        $this->adminService = $adminService;
        $this->middleware(['auth', 'role:superadmin']);
    }

    /**
     * Display a listing of schools with filtering options
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        try {
            $regions = Cache::remember('regions', self::CACHE_TTL, function() {
                return Region::orderBy('name')->get();
            });

            $sectors = Cache::remember('sectors', self::CACHE_TTL, function() {
                return Sector::with('region')->orderBy('name')->get();
            });

            $query = School::query();
            
            $query->with([
                'sector.region',
                'admin',
                'admins',
                'data' => function($query) {
                    $query->latest();
                }
            ]);

            $query = $this->applyFilters($query, $request);
            $schools = $query->orderBy('name')->paginate(self::PAGINATION_LIMIT)->withQueryString();

            return view('pages.settings.personal.schools.index', [
                'schools' => $schools,
                'regions' => $regions,
                'sectors' => $sectors,
                'schoolTypes' => SchoolStatus::cases(),
                'filters' => $request->all()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load schools index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('pages.settings.personal.schools.index')
                ->with('error', 'Məktəbləri yükləyərkən xəta baş verdi');
        }
    }

    /**
     * Show school creation form
     *
     * @return View
     */
    public function create(): View
    {
        $sectors = Cache::remember('sectors_with_regions', self::CACHE_TTL, function() {
            return Sector::with('region')
                ->where('status', true)
                ->orderBy('name')
                ->get();
        });

        return view('pages.settings.personal.schools.create', [
            'sectors' => $sectors,
            'schoolTypes' => SchoolStatus::cases()
        ]);
    }

    /**
     * Store a newly created school
     *
     * @param StoreSchoolRequest $request
     * @return JsonResponse
     */
    public function store(StoreSchoolRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $school = $this->schoolService->create($request->validated());

            if ($request->has('admin_id')) {
                $this->adminService->assignToSchool($school, $request->admin_id);
            }

            DB::commit();
            Cache::tags(['schools', 'school_stats'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Məktəb uğurla yaradıldı',
                'data' => $school->load('sector.region', 'admin')
            ]);

        } catch (SchoolException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('School creation failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Məktəb yaradılarkən xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Show school edit form
     *
     * @param School $school
     * @return View
     */
    public function edit(School $school): View
    {
        $school->load(['sector.region', 'admin', 'data.category']);
        
        // Get available admins who are not assigned to any school
        $availableAdmins = User::where('user_type', UserType::SCHOOL_ADMIN)
            ->whereDoesntHave('school')
            ->orWhere('id', $school->admin_id) // Include current admin if exists
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('pages.settings.personal.schools.edit', [
            'school' => $school,
            'sectors' => Sector::with('region')->where('status', true)->get(),
            'schoolTypes' => SchoolStatus::cases(),
            'categories' => Category::with('fields')->get(),
            'dataCompletion' => $school->calculateDataCompletion(),
            'availableAdmins' => $availableAdmins
        ]);
    }

    /**
     * Update school information
     *
     * @param UpdateSchoolRequest $request
     * @param School $school
     * @return JsonResponse
     */
    public function update(UpdateSchoolRequest $request, School $school): JsonResponse
    {
        try {
            DB::beginTransaction();

            $school = $this->schoolService->update($school, $request->validated());

            if ($request->has('admin_id') && $request->admin_id !== $school->admin_id) {
                $this->adminService->updateSchoolAdmin($school, $request->admin_id);
            }

            DB::commit();
            Cache::tags(['schools', 'school_stats'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Məktəb məlumatları yeniləndi',
                'data' => $school->fresh(['sector.region', 'admin'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('School update failed', [
                'school_id' => $school->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Məktəb məlumatlarını yeniləyərkən xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Delete a school
     *
     * @param School $school
     * @return JsonResponse
     */
    public function destroy(School $school): JsonResponse
    {
        try {
            $this->schoolService->delete($school);
            Cache::tags(['schools', 'school_stats'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Məktəb uğurla silindi'
            ]);

        } catch (SchoolException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Create a new school admin
     *
     * @param CreateSchoolAdminRequest $request
     * @return JsonResponse
     */
    public function createAdmin(CreateSchoolAdminRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Default UTİS kodu təyin et
            $utisCode = $request->utis_code ?? '0000000';

            // Unikallıq yoxlamaları
            $this->validateUniqueFields($request);

            $admin = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'utis_code' => $utisCode,
                'phone' => $request->phone,
                'user_type' => UserType::SCHOOL_ADMIN,
                'is_active' => true,
                'sector_id' => $request->sector_id,
                'school_id' => $request->school_id
            ]);

            // Əsas admin rolunu təyin etmə
            $this->assignSchoolAdminRole($admin);

            DB::commit();

            $this->logAdminCreation($admin);

            return response()->json([
                'success' => true,
                'message' => 'Məktəb administratoru uğurla yaradıldı',
                'data' => $admin->load('roles')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->logAdminCreationFailure($e, $request);

        return response()->json([
            'success' => false,
            'message' => 'Məktəb administratoru yaradılarkən xəta baş verdi',
            'error_details' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
    }

    /**
 * Unikallıq yoxlamaları üçün ayrıca metod
 */
    private function validateUniqueFields(CreateSchoolAdminRequest $request): void
    {
        $uniqueChecks = [
            'utis_code' => User::where('utis_code', $request->utis_code)->first(),
            'email' => User::where('email', $request->email)->first(),
            'username' => User::where('username', $request->username)->first()
        ];

        $errorMessages = [
            'utis_code' => 'Bu UTİS kodu artıq istifadə olunub',
            'email' => 'Bu email artıq qeydiyyatdan keçib',
            'username' => 'Bu istifadəçi adı artıq mövcuddur'
        ];

        foreach ($uniqueChecks as $field => $existingUser) {
            if ($existingUser) {
                throw new \Exception($errorMessages[$field]);
            }
        }
    }

    /**
 * Admin rolunu təyin etmə
 */
    private function assignSchoolAdminRole(User $admin): void
    {
        $schoolAdminRole = Role::where('name', 'school-admin')->first();
        if ($schoolAdminRole) {
            $admin->roles()->attach($schoolAdminRole);
        }
    }

    /**
 * Admin yaradılmasını loqlama
 */
    private function logAdminCreation(User $admin): void
    {
        Log::info('School admin created', [
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'username' => $admin->username
        ]);
    }

    /**
 * Admin yaradılması xətasını loqlama
 */
    private function logAdminCreationFailure(\Exception $e, CreateSchoolAdminRequest $request): void
    {
        Log::error('School admin creation failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request->except('password')
        ]);
    }

    /**
     * Remove admin from school
     *
     * @param School $school
     * @return JsonResponse
     */
    public function removeAdmin(School $school): JsonResponse
    {
        try {
            $this->adminService->removeFromSchool($school);
            Cache::tags(['schools', 'admins'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Admin uğurla silindi'
            ]);

        } catch (AdminAssignmentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Perform bulk actions on schools
     *
     * @param BulkActionRequest $request
     * @return JsonResponse
     */
    public function bulkAction(BulkActionRequest $request): JsonResponse
    {
        try {
            $result = $this->schoolService->processBulkAction(
                $request->action,
                $request->school_ids
            );

            Cache::tags(['schools'])->flush();

            return response()->json([
                'success' => true,
                'message' => 'Əməliyyat uğurla yerinə yetirildi',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk action failed', [
                'action' => $request->action,
                'schools' => $request->school_ids,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Əməliyyat zamanı xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Apply filters to school query
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    private function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('region_id')) {
            $query->whereHas('sector', function($q) use ($request) {
                $q->where('region_id', $request->region_id);
            });
        }

        if ($request->filled('sector_id')) {
            $query->where('sector_id', $request->sector_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        return $query;
    }

    /**
     * Get sectors for a region
     *
     * @param int $regionId
     * @return JsonResponse
     */
    public function getSectorsByRegion(int $regionId): JsonResponse
    {
        try {
            $sectors = Cache::remember(
                "region_{$regionId}_sectors",
                self::CACHE_TTL,
                function() use ($regionId) {
                    return Sector::where('region_id', $regionId)
                        ->where('status', true)
                        ->select(['id', 'name'])
                        ->orderBy('name')
                        ->get();
                }
            );

            return response()->json([
                'success' => true,
                'data' => $sectors
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch sectors', [
                'region_id' => $regionId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sektorları yükləyərkən xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Show school data management page
     *
     * @param School $school
     * @return View
     */
    public function showData(School $school): View
    {
        try {
            $school->load(['data.category', 'sector.region']);
            $categories = Cache::remember('data_categories', self::CACHE_TTL, function() {
                return Category::with('fields')->get();
            });
            
            return view('pages.settings.personal.schools.data', [
                'school' => $school,
                'data' => $school->data()->paginate(self::PAGINATION_LIMIT),
                'categories' => $categories,
                'dataCompletion' => $school->calculateDataCompletion()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to load school data page', [
                'school_id' => $school->id,
                'error' => $e->getMessage()
            ]);
            
            return back()->with('error', 'Səhifəni yükləyərkən xəta baş verdi.');
        }
    }

    /**
     * Update school data
     *
     * @param UpdateSchoolDataRequest $request
     * @param School $school
     * @return JsonResponse
     */
    public function updateData(UpdateSchoolDataRequest $request, School $school): JsonResponse
    {
        try {
            DB::beginTransaction();
            
            $school->data()->delete();
            $school->data()->createMany(
                collect($request->validated()['data'])->map(function($value, $key) {
                    return ['field_id' => $key, 'value' => $value];
                })
            );
            
            DB::commit();
            Cache::tags(['schools', 'school_stats'])->flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Məlumatlar uğurla yeniləndi!',
                'data_completion' => $school->calculateDataCompletion()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update school data', [
                'school_id' => $school->id,
                'error' => $e->getMessage(),
                'request' => $request->validated()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Məlumatları yeniləyərkən xəta baş verdi!'
            ], 500);
        }
    }




}