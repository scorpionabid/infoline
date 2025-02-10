<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Rolları yoxlayın
        foreach ($roles as $role) {
            $role = str_replace('_', '-', $role);
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        abort(403, 'Bu əməliyyatı yerinə yetirmək üçün səlahiyyətiniz yoxdur');
    }
}