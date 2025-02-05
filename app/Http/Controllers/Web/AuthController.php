<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domain\Entities\User; // User entity-ni burdan import edirik
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

class AuthController extends Controller
{
    /**
     * Login səhifəsinin göstərilməsi
     */
    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    /**
     * Login əməliyyatı
     */
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                
                // İstifadəçinin roluna görə yönləndirmə
                $user = Auth::user();
                
                if ($user->isSuperAdmin()) {
                    return redirect()->route('dashboard.super-admin');
                } elseif ($user->isSectorAdmin()) {
                    return redirect()->route('dashboard.sector-admin');
                } else {
                    return redirect()->route('dashboard.school-admin');
                }
            }

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Daxil etdiyiniz email və ya şifrə yanlışdır.',
                ]);

        } catch (\Exception $e) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Giriş zamanı xəta baş verdi. Yenidən cəhd edin.',
                ]);
        }
    }

    /**
     * Logout əməliyyatı
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}