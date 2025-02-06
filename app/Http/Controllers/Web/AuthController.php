<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        \Log::info('Sistemə giriş cəhdi', [
            'email' => $credentials['email'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            \Log::info('Uğurlu giriş', [
                'user_id' => $user->id,
                'email' => $user->email,
                'user_type' => $user->user_type
            ]);
            
            // Rola görə yönləndirmə
            switch ($user->user_type) {
                case 'superadmin':
                    return redirect()->route('dashboard.super-admin')
                        ->with('success', 'Sistemə uğurla daxil oldunuz!');
                case 'sectoradmin':
                    return redirect()->route('dashboard.sector-admin')
                        ->with('success', 'Sistemə uğurla daxil oldunuz!');
                case 'schooladmin':
                    return redirect()->route('dashboard.school-admin')
                        ->with('success', 'Sistemə uğurla daxil oldunuz!');
                default:
                    return redirect()->route('dashboard')
                        ->with('success', 'Sistemə uğurla daxil oldunuz!');
            }
        }

        \Log::warning('Uğursuz giriş cəhdi', [
            'email' => $credentials['email'],
            'ip' => $request->ip()
        ]);

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Daxil etdiyiniz məlumatlar səhvdir.',
                'credentials' => 'Bu email və ya şifrə ilə istifadəçi tapılmadı.'
            ]);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        \Log::info('İstifadəçi sistemdən çıxış etdi', [
            'user_id' => $user?->id,
            'email' => $user?->email
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/')
            ->with('success', 'Sistemdən uğurla çıxış etdiniz!');
    }
}