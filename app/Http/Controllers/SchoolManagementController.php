<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Domain\Entities\School;
use App\Domain\Entities\Sector;

class SchoolManagementController extends Controller
{
    public function index()
    {
        $schools = School::with('sector')->paginate(20);
        return view('settings.schools.index', compact('schools'));
    }

    public function create()
    {
        $sectors = Sector::all();
        return view('settings.schools.create', compact('sectors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:schools|max:255',
            'sector_id' => 'required|exists:sectors,id',
            'utis_code' => 'required|unique:schools',
            'phone' => 'nullable|string',
            'email' => 'nullable|email|unique:schools'
        ]);

        $school = School::create($validated);

        return redirect()
            ->route('settings.schools.index')
            ->with('success', 'Məktəb uğurla yaradıldı');
    }

    public function edit(School $school)
    {
        $sectors = Sector::all();
        return view('settings.schools.edit', compact('school', 'sectors'));
    }

    public function update(Request $request, School $school)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:schools,name,' . $school->id,
            'sector_id' => 'required|exists:sectors,id',
            'utis_code' => 'required|unique:schools,utis_code,' . $school->id,
            'phone' => 'nullable|string',
            'email' => 'nullable|email|unique:schools,email,' . $school->id
        ]);

        $school->update($validated);

        return redirect()
            ->route('settings.schools.index')
            ->with('success', 'Məktəb məlumatları yeniləndi');
    }

    public function destroy(School $school)
    {
        $school->delete();

        return back()->with('success', 'Məktəb silindi');
    }

    public function updateStatus(Request $request, School $school)
    {
        $school->update([
            'is_active' => $request->status
        ]);

        return back()->with('success', 'Məktəbin statusu yeniləndi');
    }

    public function assignAdmin(Request $request, School $school)
    {
        // Admin təyinatı üçün məntiq
        $validated = $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

        $school->update([
            'admin_id' => $validated['admin_id']
        ]);

        return back()->with('success', 'Məktəbə admin təyin edildi');
    }
}