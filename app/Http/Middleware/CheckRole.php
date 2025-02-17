<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            Log::warning('Unauthenticated access attempt', [
                'path' => $request->path(),
                'ip' => $request->ip()
            ]);

            return $this->handleUnauthorizedAccess($request);
        }

        $user = Auth::user();

        // Check rate limiting before role verification
        if ($this->exceedsRateLimit($request)) {
            return $this->handleTooManyAttempts($request);
        }

        // Role and user type verification
        if ($this->hasRequiredRoleOrType($user, $roles)) {
            // Log successful access
            Log::info('Authorized access', [
                'user_id' => $user->id,
                'path' => $request->path(),
                'roles' => $roles
            ]);

            return $next($request);
        }

        // Log unauthorized access attempt
        Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'path' => $request->path(),
            'required_roles' => $roles
        ]);

        return $this->handleUnauthorizedAccess($request);
    }

    /**
     * Check if user has required role or user type.
     *
     * @param  \App\Domain\Entities\User  $user
     * @param  array  $roles
     * @return bool
     */
    protected function hasRequiredRoleOrType($user, array $roles): bool
    {
        foreach ($roles as $role) {
            // Check user type and role
            if (
                $user->user_type === $role || 
                $user->hasRole($role) || 
                $user->hasPermission($role)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Handle rate limiting for requests.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function exceedsRateLimit($request): bool
    {
        $key = $this->throttleKey($request);
        
        // 5 attempts per minute
        return RateLimiter::tooManyAttempts($key, 5, 60);
    }

    /**
     * Generate a unique throttle key for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey($request): string
    {
        return 'role_check_' . $request->ip();
    }

    /**
     * Handle too many attempts scenario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function handleTooManyAttempts($request)
    {
        Log::warning('Too many role check attempts', [
            'ip' => $request->ip()
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Çox sayda uğursuz cəhd. Bir müddət sonra yenidən cəhd edin.'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        return redirect('login')->with('error', 'Çox sayda uğursuz cəhd. Bir müddət sonra yenidən cəhd edin.');
    }

    /**
     * Handle unauthorized access scenarios.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function handleUnauthorizedAccess($request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'Bu əməliyyatı yerinə yetirmək üçün səlahiyyətiniz yoxdur'
            ], Response::HTTP_FORBIDDEN);
        }

        abort(403, 'Bu əməliyyatı yerinə yetirmək üçün səlahiyyətiniz yoxdur');
    }
}