<?php

namespace App\Http\Controllers\Settings\Personal;
use AppHttpControllersController;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        return view('pages.profile.index');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . auth()->id()],
        ]);

        auth()->user()->update($validated);

        return back()->with('success', 'Profil məlumatları yeniləndi');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        auth()->user()->update([
            'password' => bcrypt($validated['password'])
        ]);

        return back()->with('success', 'Şifrə uğurla yeniləndi');
    }
}