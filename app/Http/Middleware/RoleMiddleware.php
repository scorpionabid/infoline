<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Domain\Enums\UserType;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $userType = match ($role) {
            'super-admin' => UserType::SUPER_ADMIN,
            'sector-admin' => UserType::SECTOR_ADMIN,
            'school-admin' => UserType::SCHOOL_ADMIN,
            default => null
        };

        if (!$userType || $request->user()->user_type !== $userType) {
            abort(403, 'Bu səhifəyə giriş icazəniz yoxdur.');
        }

        return $next($request);
    }
}
