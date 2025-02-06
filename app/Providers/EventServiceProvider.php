<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Log\Events\MessageLogged;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [];

    public function boot(): void
    {
        // Log Event Listener
        Event::listen(MessageLogged::class, function (MessageLogged $event) {
            // 20 dəqiqədən köhnə logları silmək üçün check
            $this->cleanOldLogs();
        });
    }

    private function cleanOldLogs(): void
    {
        $logPath = storage_path('logs');
        $threshold = now()->subMinutes(20);

        // Log fayllarını yoxla
        foreach (glob($logPath . '/*.log') as $file) {
            if (filemtime($file) < $threshold->timestamp) {
                unlink($file);
            }
        }
    }
}