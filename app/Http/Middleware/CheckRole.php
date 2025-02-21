<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string ...$roles
     * @return Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();
        
        // user_type-ı yoxlayaq
        foreach ($roles as $role) {
            // role adlarını normalize edək
            $roleType = str_replace('-', '', $role); // 'super-admin' -> 'superadmin'
            
            if ($user->user_type === $roleType) {
                return $next($request);
            }
        }

        \Log::warning('Unauthorized access attempt', [
            'user_id' => $user->id,
            'path' => $request->path(),
            'user_type' => $user->user_type,
            'required_roles' => $roles
        ]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        abort(403, 'Bu səhifəyə giriş icazəniz yoxdur.');
    }
}