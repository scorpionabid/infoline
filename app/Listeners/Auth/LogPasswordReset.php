<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordResetEvent;
use Illuminate\Support\Facades\Log;

class LogPasswordReset
{
    public function handle(PasswordResetEvent $event)
    {
        Log::info('Password reset successful', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'timestamp' => now()
        ]);
    }
}