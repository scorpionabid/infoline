<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use App\Domain\Entities\Region;
use App\Domain\Entities\User;
use App\Domain\Entities\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Settings\School\StoreSchoolRequest;
use App\Http\Requests\Settings\School\UpdateSchoolRequest;
use App\Http\Requests\Settings\School\AssignAdminRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class SchoolManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = School::with(['sector.region', 'admin'])
            ->withCount(['data', 'admins']);

        // Filtirləmə
        if ($request->filled('region')) {
            $query->inRegion($request->region);
        }

        if ($request->filled('sector')) {
            $query->where('sector_id', $request->sector);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status === 'active');
        }

        if ($request->filled('type')) {
            $query->ofType($request->type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('utis_code', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $schools = $query->latest()->paginate(20);
        $regions = Region::all();
        $sectors = Sector::all();
        $schoolTypes = config('enums.school_types');

        return view('pages.settings.personal.schools.index', compact(
            'schools', 
            'regions', 
            'sectors',
            'schoolTypes'
        ));
    }

    public function create()
    {
        $sectors = Sector::with('region')->get();
        $schoolTypes = config('enums.school_types');
        
        return view('pages.settings.personal.schools.create', compact('sectors', 'schoolTypes'));
    }

    public function store(StoreSchoolRequest $request)
    {
        try {
            DB::beginTransaction();

            $school = School::create($request->validated());

            // Əgər admin təyin edilibsə
            if ($request->filled('admin_id')) {
                $school->assignAdmin(User::findOrFail($request->admin_id));
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Məktəb uğurla yaradıldı',
                    'school' => $school->load('sector.region', 'admin')
                ]);
            }

            return redirect()
                ->route('settings.personal.schools.index')
                ->with('success', 'Məktəb uğurla yaradıldı');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Məktəb yaradılarkən xəta: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xəta baş verdi: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function edit(School $school)
    {
        $sectors = Sector::with('region')->get();
        $schoolTypes = config('enums.school_types');
        $categories = Category::all();
        $dataCompletion = $school->data_completion_percentage;

        return view('pages.settings.personal.schools.edit', compact(
            'school',
            'sectors',
            'schoolTypes',
            'categories',
            'dataCompletion'
        ));
    }

    public function update(UpdateSchoolRequest $request, School $school)
    {
        try {
            DB::beginTransaction();

            $school->update($request->validated());

            // Əgər admin dəyişdirilibsə
            if ($request->filled('admin_id') && $request->admin_id !== $school->admin_id) {
                $school->assignAdmin(User::findOrFail($request->admin_id));
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Məktəb məlumatları yeniləndi',
                    'school' => $school->fresh(['sector.region', 'admin'])
                ]);
            }

            return redirect()
                ->route('settings.personal.schools.index')
                ->with('success', 'Məktəb məlumatları yeniləndi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Məktəb yenilənərkən xəta: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xəta baş verdi: ' . $e->getMessage()
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Xəta baş verdi: ' . $e->getMessage());
        }
    }

    public function destroy(School $school)
    {
        try {
            if ($school->data()->exists()) {
                throw new \Exception('Bu məktəbə aid məlumatlar var. Əvvəlcə məlumatları silin.');
            }

            $school->delete();

            return response()->json([
                'success' => true,
                'message' => 'Məktəb silindi'
            ]);

        } catch (\Exception $e) {
            Log::error('Məktəb silinərkən xəta: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function updateStatus(Request $request, School $school)
    {
        try {
            $school->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Məktəbin statusu yeniləndi'
            ]);

        } catch (\Exception $e) {
            Log::error('Məktəb statusu yenilənərkən xəta: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Xəta baş verdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function assignAdmin(AssignAdminRequest $request, School $school)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($request->admin_id);
            $school->assignAdmin($user);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Məktəbə admin təyin edildi',
                'admin' => $user
            ]);

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'İstifadəçi tapılmadı'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Admin təyin edilərkən xəta: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Xəta baş verdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSchoolData(School $school)
    {
        $data = $school->data()
            ->with('category')
            ->latest()
            ->paginate(20);

        return view('pages.settings.personal.schools.data', compact('school', 'data'));
    }

    /**
     * Məktəb məlumatları səhifəsini göstərir
     */
    public function data(School $school)
    {
        $categories = Category::all();
        $data = $school->data()->with('category')->latest()->paginate(10);
        $dataCompletion = $this->calculateDataCompletion($school);

        return view('pages.settings.personal.schools.data', compact('school', 'categories', 'data', 'dataCompletion'));
    }

    /**
     * Yeni məktəb məlumatı əlavə edir
     */
    public function storeData(Request $request, School $school)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string|max:1000',
        ]);

        $school->data()->create($validated);

        return response()->json([
            'message' => 'Məlumat uğurla əlavə edildi'
        ]);
    }

    /**
     * Məktəb məlumatını yeniləyir
     */
    public function updateData(Request $request, $id)
    {
        $data = SchoolData::findOrFail($id);
        
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'content' => 'required|string|max:1000',
        ]);

        $data->update($validated);

        return response()->json([
            'message' => 'Məlumat uğurla yeniləndi'
        ]);
    }

    /**
     * Məktəb məlumatını silir
     */
    public function destroyData($id)
    {
        $data = SchoolData::findOrFail($id);
        $data->delete();

        return response()->json([
            'message' => 'Məlumat uğurla silindi'
        ]);
    }

    /**
     * Məktəb məlumatlarının tamamlanma faizini hesablayır
     */
    private function calculateDataCompletion(School $school)
    {
        $totalCategories = Category::count();
        $completedCategories = $school->data()->distinct('category_id')->count('category_id');

        return round(($completedCategories / $totalCategories) * 100);
    }
}