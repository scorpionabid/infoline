<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DebugController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

// Guest/Public routes
Route::middleware('guest')->group(function () {
   // Ana səhifə redirecti
   Route::get('/', function () {
       return redirect()->route('login');
   });
   
   // Auth routes
   Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
   Route::post('/login', [AuthController::class, 'login']);
   // routes/web.php-də əlavə edək:
   Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
   Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
});

// Protected/Authenticated routes
Route::middleware('auth')->group(function () {
   // Logout
   Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
   
   // Dashboard routes
   Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

   // SuperAdmin dashboard
   Route::get('/dashboard/super-admin', [DashboardController::class, 'superAdmin'])
       ->middleware('role:super_admin')
       ->name('dashboard.super-admin');

   // SectorAdmin dashboard
   Route::get('/dashboard/sector-admin', [DashboardController::class, 'sectorAdmin'])
       ->middleware('role:sector_admin')
       ->name('dashboard.sector-admin');

   // SchoolAdmin dashboard
   Route::get('/dashboard/school-admin', [DashboardController::class, 'schoolAdmin'])
       ->middleware('role:school_admin')
       ->name('dashboard.school-admin');

   // Settings routes
   Route::prefix('settings')->group(function () {
       Route::get('/', [SettingsController::class, 'index'])->name('settings');
       
       // Categories (only for super_admin)
       Route::middleware('role:super_admin')->group(function () {
           Route::get('/categories', [SettingsController::class, 'categories'])->name('settings.categories');
           Route::get('/schools', [SettingsController::class, 'schools'])->name('settings.schools');
       });

       // School settings (for school_admin)
       Route::middleware('role:school_admin')->group(function () {
           Route::get('/school', [SettingsController::class, 'school'])->name('settings.school');
       });

       // Sector settings (for sector_admin)
       Route::middleware('role:sector_admin')->group(function () {
           Route::get('/sector', [SettingsController::class, 'sector'])->name('settings.sector');
       });
       
        Route::get('/test-log', function () {
            Log::info('Test log message', [
             'user_id' => auth()->id(),
             'timestamp' => now()->toDateTimeString()
            ]);
            return 'Test log created';
        })->name('test.log');
       
   });

   // Profile routes
   Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
   Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
   Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

   // API Documentation (only for super_admin)
   Route::get('/api-docs', function () {
       return view('pages.api-docs');
   })->middleware('role:super_admin')->name('api-docs');
   
});

// Fallback route
Route::fallback(function () {
   return view('pages.errors.404');
});

// Debug routes (yalnız development zamanı)
if (app()->environment('local')) {
   Route::get('/debug/user', [DebugController::class, 'checkUser']);
   Route::get('/debug/password', [DebugController::class, 'checkPassword']);
}