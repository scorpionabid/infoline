<?php

namespace App\Listeners\Auth;

use App\Events\Auth\LoginEvent;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogin
{
    public function handle(LoginEvent $event): void
    {
        $user = $event->user;
        
        // Login zamanını yenilə
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip()
        ]);

        Log::info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => request()->ip(),
            'is_api' => $event->isApi
        ]);
    }
}