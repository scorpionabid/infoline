<?php

namespace App\Http\Controllers\Settings\Personal;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Domain\Entities\School;
use App\Domain\Entities\Sector;
use App\Domain\Entities\User;

class SchoolManagementController extends Controller
{
    public function index()
    {
        $schools = School::with('sector')->paginate(20);
        $schoolAdmins = User::where('user_type', 'schooladmin')
            ->with('school')
            ->paginate(20);
        return view('pages.settings.personal.schools.index', compact('schools', 'schoolAdmins'));
    }

    public function create()
    {
        $sectors = Sector::all();
        return view('pages.settings.personal.schools.create', compact('sectors'));
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