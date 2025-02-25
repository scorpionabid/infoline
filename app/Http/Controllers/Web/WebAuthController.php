<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domain\Enums\UserType;
use App\Domain\Entities\User;
use App\Events\Auth\LoginEvent;
use App\Events\Auth\LogoutEvent;
use App\Events\Auth\FailedLoginAttemptEvent;
use App\Events\Auth\PasswordResetEvent;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Http\Requests\Auth\PasswordUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WebAuthController extends Controller
{
    /**
     * Login formasını göstərir
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }
        return view('auth.login');
    }

    /**
     * İstifadəçi girişini həyata keçirir
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required']
            ]);

            // Remember me checkbox-dan gələn dəyəri boolean-a çeviririk
            $remember = $request->has('remember');

            // Debug məlumatları
            Log::info('Login attempt', [
                'email' => $credentials['email'],
                'remember' => $remember,
                'session_id' => $request->session()->getId(),
                'session_driver' => config('session.driver')
            ]);

            if ($this->hasTooManyLoginAttempts($request)) {
                event(new FailedLoginAttemptEvent(
                    $request->email,
                    $request->ip(),
                    $this->getLoginAttempts($request)
                ));
                
                return $this->sendLockoutResponse($request);
            }

            // Əvvəlcə istifadəçini tapaq
            $user = User::where('email', $credentials['email'])->first();
            
            // Debug məlumatları
            if ($user) {
                Log::info('User found:', [
                    'id' => $user->id,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'is_active' => $user->is_active
                ]);
            } else {
                Log::warning('User not found:', [
                    'email' => $credentials['email']
                ]);
            }

            if (Auth::attempt($credentials, $remember)) {
                $request->session()->regenerate();
                
                $user = Auth::user();
                
                Log::info('Login successful', [
                    'user_id' => $user->id,
                    'session_id' => $request->session()->getId()
                ]);
                
                // Login məlumatlarını yeniləyirik
                $user->last_login_at = now();
                $user->last_login_ip = $request->ip();
                $user->save();
                
                event(new LoginEvent($user));
                
                return $this->redirectBasedOnRole($user);
            }

            // Login uğursuz oldu
            Log::warning('Login failed', [
                'email' => $credentials['email'],
                'ip' => $request->ip()
            ]);

            $this->incrementLoginAttempts($request);
            
            event(new FailedLoginAttemptEvent(
                $request->email,
                $request->ip(),
                $this->getLoginAttempts($request)
            ));

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => __('auth.failed')]);

        } catch (\Exception $e) {
            Log::error('Login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Sistemə giriş zamanı xəta baş verdi.']);
        }
    }

    /**
     * İstifadəçi çıxışını həyata keçirir
     */
    public function logout(Request $request)
    {
        event(new LogoutEvent(Auth::user()));
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }

    /**
     * Şifrə bərpası formasını göstərir
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Şifrə bərpası linki göndərir
     */
    public function sendPasswordResetLink(PasswordResetRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Şifrə yeniləmə formasını göstərir
     */
    public function showResetPasswordForm(Request $request)
    {
        return view('auth.reset-password', ['token' => $request->token]);
    }

    /**
     * Şifrəni yeniləyir
     */
    public function resetPassword(PasswordUpdateRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordResetEvent($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * İstifadəçini roluna görə yönləndirir
     */
    protected function redirectBasedOnRole($user)
    {
        if ($user->hasRole('super')) {
            return redirect()->route('dashboard.super-admin');
        } elseif ($user->hasRole('sector')) {
            return redirect()->route('dashboard.sector-admin');
        } elseif ($user->hasRole('school')) {
            return redirect()->route('dashboard.school-admin');
        }
        
        return redirect()->route('dashboard');
    }

    /**
     * Login cəhdlərinin sayını artırır
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);
        $attempts = cache()->get($key, 0) + 1;
        cache()->put($key, $attempts, now()->addMinutes(60));
    }

    /**
     * Login cəhdlərinin sayını yoxlayır
     */
    protected function hasTooManyLoginAttempts(Request $request)
    {
        return cache()->get($this->throttleKey($request), 0) >= 5;
    }

    /**
     * Login cəhdlərinin sayını qaytarır
     */
    protected function getLoginAttempts(Request $request)
    {
        return cache()->get($this->throttleKey($request), 0);
    }

    /**
     * Cache key-ni generasiya edir
     */
    protected function throttleKey(Request $request)
    {
        return 'login_attempts_' . $request->ip();
    }

    /**
     * Bloklanma cavabını qaytarır
     */
    protected function sendLockoutResponse(Request $request)
    {
        return back()->withErrors([
            'email' => ['Həddindən artıq cəhd. Zəhmət olmasa bir neçə dəqiqə gözləyin.'],
        ]);
    }
}