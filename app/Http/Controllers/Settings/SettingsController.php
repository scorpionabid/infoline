<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Domain\Entities\{Category, Column};
use App\Domain\Entities\{Region, Sector, School, User};
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    // ... existing code ...

    /**
     * System settings page
     */
    public function system()
    {
        return view('pages.settings.system.index', [
            'notifications' => $this->getNotificationSettings(),
            'backups' => $this->getBackups(),
            'logs' => $this->getLogs()
        ]);
    }

    /**
     * Notification settings page
     */
    public function notifications()
    {
        return view('pages.settings.system.notifications', [
            'settings' => $this->getNotificationSettings()
        ]);
    }

    /**
     * Update notification settings
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'deadline_reminders' => 'boolean',
            'system_alerts' => 'boolean',
            'reminder_days' => 'required|integer|min:1|max:30'
        ]);

        // Update settings in database or config
        setting()->set($validated);
        setting()->save();

        return response()->json([
            'message' => 'Bildiriş tənzimləmələri uğurla yeniləndi'
        ]);
    }

    /**
     * Backup management page
     */
    public function backups()
    {
        return view('pages.settings.system.backups', [
            'backups' => $this->getBackups()
        ]);
    }

    /**
     * Create new backup
     */
    public function createBackup(Request $request)
    {
        try {
            // Create backup using Laravel Backup package
            \Artisan::call('backup:run');

            return response()->json([
                'message' => 'Yeni backup uğurla yaradıldı'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Backup yaradılarkən xəta baş verdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup($filename)
    {
        $path = storage_path("app/backups/{$filename}");

        if (!file_exists($path)) {
            abort(404, 'Backup faylı tapılmadı');
        }

        return response()->download($path);
    }

    /**
     * Delete backup file
     */
    public function deleteBackup($filename)
    {
        $path = storage_path("app/backups/{$filename}");

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'Backup faylı tapılmadı'
            ], 404);
        }

        unlink($path);

        return response()->json([
            'message' => 'Backup faylı uğurla silindi'
        ]);
    }

    /**
     * Log management page
     */
    public function logs()
    {
        return view('pages.settings.system.logs', [
            'logs' => $this->getLogs()
        ]);
    }

    /**
     * View specific log file
     */
    public function viewLog($filename)
    {
        $path = storage_path("logs/{$filename}");

        if (!file_exists($path)) {
            abort(404, 'Log faylı tapılmadı');
        }

        $content = file_get_contents($path);

        return view('pages.settings.system.log-viewer', [
            'filename' => $filename,
            'content' => $content
        ]);
    }

    /**
     * Delete log file
     */
    public function deleteLog($filename)
    {
        $path = storage_path("logs/{$filename}");

        if (!file_exists($path)) {
            return response()->json([
                'message' => 'Log faylı tapılmadı'
            ], 404);
        }

        unlink($path);

        return response()->json([
            'message' => 'Log faylı uğurla silindi'
        ]);
    }

    /**
     * Table settings page
     */
    public function table(Request $request)
    {
        $categories = Category::orderBy('name')->get();
        $selectedCategory = null;
        $columns = collect();

        if ($categoryId = $request->query('category')) {
            $selectedCategory = Category::find($categoryId);
            if ($selectedCategory) {
                $columns = Column::where('category_id', $categoryId)
                    ->orderBy('order')
                    ->get();
            }
        }

        return view('pages.settings.table.index', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'columns' => $columns
        ]);
    }

    /**
     * Settings index page
     */
    public function index()
    {
        return view('pages.settings.index');
    }

    /**
     * Personal settings page
     */
    public function personal()
    {
        // Statistika məlumatlarını əldə edirik
        $regions_count = Region::count();
        $sectors_count = Sector::count();
        $schools_count = School::count();
        $users_count = User::count();

        // Son fəaliyyətləri əldə edirik
        $activities = Activity::with('causer')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('pages.settings.personal.index', compact(
            'regions_count',
            'sectors_count',
            'schools_count',
            'users_count',
            'activities'
        ));
    }

    /**
     * Get notification settings
     */
    private function getNotificationSettings()
    {
        return [
            'email_notifications' => setting('email_notifications', true),
            'deadline_reminders' => setting('deadline_reminders', true),
            'system_alerts' => setting('system_alerts', true),
            'reminder_days' => setting('reminder_days', 7)
        ];
    }

    /**
     * Get list of backup files
     */
    private function getBackups()
    {
        $path = storage_path('app/backups');
        
        if (!file_exists($path)) {
            return [];
        }

        $files = array_diff(scandir($path), ['.', '..']);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'name' => $file,
                'size' => filesize("{$path}/{$file}"),
                'created_at' => date('Y-m-d H:i:s', filemtime("{$path}/{$file}"))
            ];
        }

        return collect($backups)->sortByDesc('created_at')->values()->all();
    }

    /**
     * Get list of log files
     */
    private function getLogs()
    {
        $path = storage_path('logs');
        $files = array_diff(scandir($path), ['.', '..']);
        $logs = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                $logs[] = [
                    'name' => $file,
                    'size' => filesize("{$path}/{$file}"),
                    'updated_at' => date('Y-m-d H:i:s', filemtime("{$path}/{$file}"))
                ];
            }
        }

        return collect($logs)->sortByDesc('updated_at')->values()->all();
    }

    // ... existing code ...
}