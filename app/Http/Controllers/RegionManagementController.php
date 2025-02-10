<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\Entities\Region;
use App\Domain\Entities\Sector;

class RegionManagementController extends Controller
{
    public function index()
    {
        $regions = Region::paginate(20);
        return view('settings.regions.index', compact('regions'));
    }

    public function create()
    {
        return view('settings.regions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:regions|max:255',
            'code' => 'nullable|unique:regions|max:50',
            'description' => 'nullable|max:500'
        ]);

        $region = Region::create($validated);

        return redirect()
            ->route('settings.regions.index')
            ->with('success', 'Region uğurla yaradıldı');
    }

    public function edit(Region $region)
    {
        return view('settings.regions.edit', compact('region'));
    }

    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:regions,name,' . $region->id,
            'code' => 'nullable|unique:regions,code,' . $region->id,
            'description' => 'nullable|max:500'
        ]);

        $region->update($validated);

        return redirect()
            ->route('settings.regions.index')
            ->with('success', 'Region məlumatları yeniləndi');
    }

    public function destroy(Region $region)
    {
        $region->delete();

        return back()->with('success', 'Region silindi');
    }

    public function addSector(Request $request, Region $region)
    {
        $validated = $request->validate([
            'sector_id' => 'required|exists:sectors,id|unique:sectors,region_id'
        ]);

        $sector = Sector::findOrFail($validated['sector_id']);
        $sector->update(['region_id' => $region->id]);

        return back()->with('success', 'Sektor regiona əlavə edildi');
    }

    public function assignAdmin(Request $request, Region $region)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

        $region->update([
            'admin_id' => $validated['admin_id']
        ]);

        return back()->with('success', 'Regiona admin təyin edildi');
    }
}