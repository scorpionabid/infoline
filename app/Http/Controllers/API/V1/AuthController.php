<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Domain\Entities\User;
use App\Events\LoginEvent;
use App\Events\LogoutEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * User login via API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                
                // Check if user is active
                if (!$user->is_active) {
                    Auth::logout();
                    return response()->json([
                        'success' => false,
                        'message' => 'Hesabınız bloklanmışdır'
                    ], 403);
                }

                // Revoke existing tokens
                $user->tokens()->delete();

                // Create new token with all abilities
                $token = $user->createToken('auth-token', ['*'], now()->addDays(7))->plainTextToken;

                // Trigger Login Event for API
                event(new LoginEvent($user, false));

                Log::info('User logged in successfully', ['user_id' => $user->id]);

                return response()->json([
                    'success' => true,
                    'token' => $token,
                    'user' => $user->load('roles.permissions', 'sector', 'school', 'region')
                ]);
            }

            Log::warning('Failed login attempt', ['email' => $request->email]);

            return response()->json([
                'success' => false,
                'message' => 'Email və ya şifrə yanlışdır'
            ], 401);

        } catch (ValidationException $e) {
            Log::warning('Login validation failed', [
                'errors' => $e->errors(),
                'email' => $request->email
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Məlumatlar düzgün deyil',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Login error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sistemdə xəta baş verdi'
            ], 500);
        }
    }

    /**
     * User registration via API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'username' => 'required|string|unique:users|alpha_dash|max:50',
                'password' => 'required|string|min:8|confirmed',
                'user_type' => 'required|in:school_admin,sector_admin,super_admin',
                'sector_id' => 'nullable|exists:sectors,id',
                'school_id' => 'nullable|exists:schools,id',
            ]);
            
            // Check authorization for creating users of specific types
            $this->authorizeUserCreation($validated['user_type']);

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'username' => $validated['username'],
                'password' => Hash::make($validated['password']),
                'user_type' => $validated['user_type'],
                'sector_id' => $validated['sector_id'] ?? null,
                'school_id' => $validated['school_id'] ?? null,
                'is_active' => true
            ]);

            // Attach default role based on user type
            $this->attachDefaultRole($user);

            // Create token with all abilities, valid for 7 days
            $token = $user->createToken('auth-token', ['*'], now()->addDays(7))->plainTextToken;
            
            Log::info('New user registered', [
                'user_id' => $user->id, 
                'user_type' => $user->user_type
            ]);

            return response()->json([
                'success' => true,
                'user' => $user->load('roles', 'sector', 'school'),
                'token' => $token
            ], 201);

        } catch (ValidationException $e) {
            Log::warning('Registration validation failed', [
                'errors' => $e->errors()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Qeydiyyat məlumatları düzgün deyil',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Registration error', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Qeydiyyat zamanı xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Get authenticated user details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'user' => $user->load(
                    'roles.permissions', 
                    'sector', 
                    'school', 
                    'region',
                    'notifications'
                )
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching user details', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'İstifadəçi məlumatları əldə edilə bilmədi'
            ], 500);
        }
    }

    /**
     * Logout user and revoke token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Trigger Logout Event for API
            event(new LogoutEvent($user, false));
            
            // Revoke the current token
            $user->currentAccessToken()->delete();

            Log::info('User logged out', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Uğurla çıxış edildi'
            ]);
        } catch (\Exception $e) {
            Log::error('Logout error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Çıxış zamanı xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Initiate password reset process.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required|email']);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? response()->json(['success' => true, 'message' => 'Şifrə bərpası linki göndərildi'])
                : response()->json(['success' => false, 'message' => 'Şifrə bərpası linki göndərilə bilmədi'], 500);

        } catch (\Exception $e) {
            Log::error('Password reset error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Şifrə bərpası zamanı xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Reset user password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        try {
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
                ? response()->json(['success' => true, 'message' => 'Şifrə uğurla dəyişdirildi'])
                : response()->json(['success' => false, 'message' => 'Şifrə bərpası mümkün olmadı'], 500);

        } catch (\Exception $e) {
            Log::error('Password reset error', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Şifrə bərpası zamanı xəta baş verdi'
            ], 500);
        }
    }

    /**
     * Authorize user creation based on current user's permissions.
     *
     * @param string $userType
     * @throws \Exception
     */
    private function authorizeUserCreation(string $userType)
    {
        $currentUser = Auth::user();

        switch ($userType) {
            case 'super_admin':
                if (!$currentUser || !$currentUser->isSuperAdmin()) {
                    throw new \Exception('Super admin yaratmaq üçün səlahiyyətiniz yoxdur');
                }
                break;
            case 'sector_admin':
                if (!$currentUser || !($currentUser->isSuperAdmin() || $currentUser->hasPermission('create-sector-admin'))) {
                    throw new \Exception('Sektor admini yaratmaq üçün səlahiyyətiniz yoxdur');
                }
                break;
            case 'school_admin':
                if (!$currentUser || !($currentUser->isSuperAdmin() || $currentUser->isSectorAdmin() || $currentUser->hasPermission('create-school-admin'))) {
                    throw new \Exception('Məktəb admini yaratmaq üçün səlahiyyətiniz yoxdur');
                }
                break;
            default:
                throw new \Exception('Yanlış istifadəçi tipi');
        }
    }

    /**
     * Attach default role based on user type.
     *
     * @param User $user
     */
    private function attachDefaultRole(User $user)
    {
        $defaultRoles = [
            'super_admin' => 'super-admin',
            'sector_admin' => 'sector-admin',
            'school_admin' => 'school-admin'
        ];

        $roleName = $defaultRoles[$user->user_type] ?? null;

        if ($roleName) {
            $role = Role::where('slug', $roleName)->first();
            if ($role) {
                $user->roles()->attach($role);
            }
        }
    }
}