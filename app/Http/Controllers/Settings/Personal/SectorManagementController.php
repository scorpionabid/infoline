<?php

namespace App\Http\Controllers\Settings\Personal;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domain\Entities\Sector;
use App\Domain\Entities\Region;
use App\Domain\Entities\School;
use App\Domain\Entities\User;

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

    public function assignAdmin(Request $request, Sector $sector)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:users,id',
            'admin_id' => [
                'exists:users,id',
                function($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!in_array($user->user_type, ['sectoradmin', 'super_admin'])) {
                        $fail('Seçilən istifadəçi sektor admini ola bilməz.');
                    }
                }
            ]
        ]);
        try {
            $sector->update([
                'admin_id' => $validated['admin_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Sektora admin təyin edildi'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Admin təyinatı zamanı xəta baş verdi'
        ], 500);
    }
    }
}