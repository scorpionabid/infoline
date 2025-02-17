<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Domain\Enums\UserType;
use App\Domain\Entities\User;
use App\Events\Auth\LoginEvent;
use App\Events\Auth\LogoutEvent;
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
     * Show login form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle user login.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Attempt login with remember me functionality
            if ($this->attemptLogin($request)) {
                $user = Auth::user();

                \Log::info('User logged in', [
                    'user_id' => $user->id,
                    'roles' => $user->roles->pluck('slug')->toArray(),
                    'has_super_admin' => $user->hasRole('super-admin'),
                    'user_type' => $user->user_type
                ]);
        

                // Check if user is active
                if (!$user->is_active) {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Hesabınız bloklanmışdır']);
                }

                // Trigger Login Event for Web
                event(new LoginEvent($user, true));

                Log::info('Web login successful', [
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);

                // Redirect based on user type
                return $this->redirectBasedOnUserType($user);
            }

            Log::warning('Web login failed', [
                'email' => $request->email,
                'ip' => $request->ip()
            ]);

            return back()->withErrors(['email' => 'Email və ya şifrə yanlışdır'])
                         ->withInput($request->only('email'));

        } catch (ValidationException $e) {
            Log::warning('Login validation failed', [
                'errors' => $e->errors(),
                'email' => $request->email
            ]);

            return back()->withErrors($e->errors())
                         ->withInput($request->only('email'));
        }
    }

    /**
     * Attempt to log in the user.
     *
     * @param Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return Auth::attempt(
            $request->only('email', 'password'),
            $request->boolean('remember')
        );
    }

    /**
     * Redirect user based on their type.
     *
     * @param \App\Domain\Entities\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    // WebAuthController.php - yenilənməli
    protected function redirectBasedOnUserType($user)
    {
        // Log əlavə edək
        \Log::info('Redirecting user', [
            'user_id' => $user->id,
            'user_type' => $user->user_type
        ]);

        try {
            switch ($user->user_type) {
                case UserType::SUPER_ADMIN:
                    return redirect()->route('dashboard.super-admin');
                case UserType::SECTOR_ADMIN:
                    if (!$user->sector_id) {
                        throw new \Exception('Sector not assigned');
                    }
                    return redirect()->route('dashboard.sector-admin');
                case UserType::SCHOOL_ADMIN:
                    if (!$user->school_id) {
                        throw new \Exception('School not assigned');
                    }
                    return redirect()->route('dashboard.school-admin');
                default:
                    throw new \Exception('Invalid user type');
            }
        } catch (\Exception $e) {
            \Log::error('Redirect error', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Hesab konfiqurasiyasında xəta var']);
        }
    }

    /**
     * Log the user out.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Trigger Logout Event for Web
        event(new LogoutEvent($user, true));

        // Log the logout event
        Log::info('Web logout', [
            'user_id' => $user->id,
            'ip' => $request->ip()
        ]);

        // Logout the user
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();
        
        // Redirect to login page
        return redirect()->route('login');
    }

    /**
     * Show forgot password form.
     *
     * @return \Illuminate\View\View
     */
    public function showForgotPasswordForm()
    {
        return view('auth.passwords.email');
    }

    /**
     * Send password reset link.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show password reset form.
     *
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function showResetPasswordForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token]);
    }

    /**
     * Handle password reset.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}