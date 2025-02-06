<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClearOldLogs extends Command
{
    protected $signature = 'logs:clear {--minutes=20}';
    protected $description = 'Clear logs older than specified minutes';

    public function handle()
    {
        $minutes = $this->option('minutes');
        $timestamp = now()->subMinutes($minutes);

        // Database loglarını təmizlə
        $deletedCount = DB::table('activity_logs')
            ->where('created_at', '<', $timestamp)
            ->delete();

        // File loglarını təmizlə
        $logPath = storage_path('logs');
        $files = glob($logPath . '/*.log');
        $fileCount = 0;

        foreach ($files as $file) {
            if (filemtime($file) < $timestamp->timestamp) {
                unlink($file);
                $fileCount++;
            }
        }

        // Nəticələri göstər
        $this->info("Deleted {$deletedCount} database logs");
        $this->info("Deleted {$fileCount} log files");

        // Log əlavə et
        Log::info("Logs cleaned", [
            'db_records_deleted' => $deletedCount,
            'files_deleted' => $fileCount,
            'older_than_minutes' => $minutes
        ]);
    }
}
