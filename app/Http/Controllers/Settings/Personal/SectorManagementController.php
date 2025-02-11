<?php

namespace App\Http\Controllers\Settings\Personal;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Domain\Entities\Sector;
use App\Domain\Entities\Region;
use App\Domain\Entities\School;

class SectorManagementController extends Controller
{
    public function index()
    {
        $sectors = Sector::with('region')->paginate(20);
        return view('pages.settings.personal.sectors.index', compact('sectors'));
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

        return redirect()
            ->route('settings.sectors.index')
            ->with('success', 'Sektor uğurla yaradıldı');
    }

    public function edit(Sector $sector)
    {
        $regions = Region::all();
        return view('settings.sectors.edit', compact('sector', 'regions'));
    }

    public function update(Request $request, Sector $sector)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:sectors,name,' . $sector->id,
            'region_id' => 'required|exists:regions,id',
            'phone' => 'nullable|string'
        ]);

        $sector->update($validated);

        return redirect()
            ->route('settings.sectors.index')
            ->with('success', 'Sektor məlumatları yeniləndi');
    }

    public function destroy(Sector $sector)
    {
        $sector->delete();

        return back()->with('success', 'Sektor silindi');
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
            'admin_id' => 'required|exists:users,id'
        ]);

        $sector->update([
            'admin_id' => $validated['admin_id']
        ]);

        return back()->with('success', 'Sektora admin təyin edildi');
    }
}