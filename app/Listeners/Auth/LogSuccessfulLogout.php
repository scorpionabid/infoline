<?php

namespace App\Listeners\Auth;

use App\Events\Auth\LogoutEvent;
use Illuminate\Support\Facades\Log;

class LogSuccessfulLogout
{
    public function handle(LogoutEvent $event): void
    {
        Log::info('User logged out', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'ip' => request()->ip()
        ]);
    }
}