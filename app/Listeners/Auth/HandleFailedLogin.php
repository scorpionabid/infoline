<?php

namespace App\Listeners\Auth;

use App\Events\Auth\FailedLoginAttemptEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class HandleFailedLogin
{
    public function handle(FailedLoginAttemptEvent $event)
    {
        // Uğursuz cəhdləri cache-də saxlayırıq
        $key = 'login_attempts_' . $event->ip;
        $attempts = Cache::get($key, 0) + 1;
        
        // 1 saat müddətində saxlayırıq
        Cache::put($key, $attempts, now()->addHour());

        // Loqa əlavə edirik
        Log::warning('Failed login attempt', [
            'email' => $event->email,
            'ip' => $event->ip,
            'attempts' => $attempts
        ]);
    }
}