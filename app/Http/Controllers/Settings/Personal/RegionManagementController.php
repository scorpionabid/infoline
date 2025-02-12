<?php

namespace App\Http\Controllers\Settings\Personal;

use App\Http\Controllers\Controller;
use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;
use Illuminate\Http\Request;

class RegionManagementController extends Controller
{
    public function index()
    {
        $regions = Region::withCount(['sectors', 'schools'])->paginate(20);
        return view('pages.settings.personal.regions.index', compact('regions'));
    }

    public function create()
    {
        return view('pages.settings.personal.regions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:regions|max:255',
            'phone' => 'nullable|unique:regions|max:20',
            'code' => 'nullable|unique:regions|max:50',
            'description' => 'nullable|max:500'
        ]);

        $region = Region::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Region uğurla yaradıldı',
                'region' => $region
            ]);
        }

        return redirect()
            ->route('settings.personal.regions.index')
            ->with('success', 'Region uğurla yaradıldı');
    }

    public function edit(Region $region)
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'region' => $region->toArray()
            ]);
        }

        return view('pages.settings.personal.regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:regions,name,' . $region->id,
            'phone' => 'nullable|max:20|unique:regions,phone,' . $region->id,
            'code' => 'nullable|max:50|unique:regions,code,' . $region->id,
            'description' => 'nullable|max:500'
        ]);

        $region->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Region məlumatları yeniləndi',
                'region' => $region
            ]);
        }

        return redirect()
            ->route('settings.personal.regions.index')
            ->with('success', 'Region məlumatları yeniləndi');
    }

    public function destroy(Region $region)
    {
        try {
            // Sektorların yoxlanması
            if ($region->sectors()->count() > 0) {
                $message = 'Bu regionda sektorlar var. Əvvəlcə sektorları silin.';
                
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                
                return back()->with('error', $message);
            }

            $region->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Region silindi'
                ]);
            }

            return back()->with('success', 'Region silindi');

        } catch (\Exception $e) {
            $message = 'Xəta baş verdi: ' . $e->getMessage();
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return back()->with('error', $message);
        }
    }

    public function addSector(Request $request, Region $region)
    {
        $validated = $request->validate([
            'sector_id' => 'required|exists:sectors,id|unique:sectors,region_id'
        ]);

        try {
            $sector = Sector::findOrFail($validated['sector_id']);
            $sector->update(['region_id' => $region->id]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sektor regiona əlavə edildi',
                    'sector' => $sector
                ]);
            }

            return back()->with('success', 'Sektor regiona əlavə edildi');

        } catch (\Exception $e) {
            $message = 'Xəta baş verdi: ' . $e->getMessage();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return back()->with('error', $message);
        }
    }

    public function assignAdmin(Request $request, Region $region)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

        try {
            $region->update([
                'admin_id' => $validated['admin_id']
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Regiona admin təyin edildi'
                ]);
            }

            return back()->with('success', 'Regiona admin təyin edildi');

        } catch (\Exception $e) {
            $message = 'Xəta baş verdi: ' . $e->getMessage();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return back()->with('error', $message);
        }
    }
}