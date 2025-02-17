<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\ClearOldLogs::class,
    ];

    protected $middleware = [
        \App\Http\Middleware\LogRequests::class,  // Bunu əlavə etməliyik
    ];

    protected function schedule(Schedule $schedule)
    {
        // Hər 5 dəqiqədən bir logları təmizlə
        $schedule->command('logs:clear --minutes=20')
                ->everyFiveMinutes()
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/scheduler.log'));
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}