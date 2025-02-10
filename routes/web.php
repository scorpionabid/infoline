<?php

/**
 * İstifadə edilən Controller-lər
 */
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DebugController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TableSettingsController;
use App\Http\Controllers\Settings\UserManagementController;
use App\Http\Controllers\SchoolManagementController;
use App\Http\Controllers\SectorManagementController;
use App\Http\Controllers\RegionManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Ana səhifə - login-ə yönləndir
    Route::get('/', function () {
        return redirect()->route('login.form');
    });

    // Giriş səhifəsi və əməliyyatları
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login.form');
    
    // Brute force hücumlarından qorunmaq üçün throttle
    Route::middleware(['throttle:login'])->group(function () {
        Route::post('/login', [AuthController::class, 'login'])
            ->name('login');
    });

    // Şifrə bərpası
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])
        ->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->name('password.email');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Çıxış
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // SuperAdmin Dashboard
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/dashboard/super-admin', [DashboardController::class, 'superAdmin'])
            ->name('dashboard.super-admin');
    });

    // Sector Admin Dashboard
    Route::middleware('role:sector_admin')->group(function () {
        Route::get('/dashboard/sector-admin', [DashboardController::class, 'sectorAdmin'])
            ->name('dashboard.sector-admin');
    });

    // School Admin Dashboard
    Route::middleware('role:school_admin')->group(function () {
        Route::get('/dashboard/school-admin', [DashboardController::class, 'schoolAdmin'])
            ->name('dashboard.school-admin');
    });

    /*
    |--------------------------------------------------------------------------
    | Settings Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->middleware(['auth', 'role:super-admin'])->name('settings.')->group(function () {
        // Ana səhifə
        Route::get('/', [SettingsController::class, 'index'])
            ->name('index');

        // Cədvəl Ayarları
        Route::prefix('table')->group(function () {
            Route::controller(TableSettingsController::class)->group(function () {
                Route::get('/', 'index')
                    ->name('table');
            
                // Kateqoriya route-ları
                Route::prefix('category')->group(function () {
                    Route::post('/', 'storeCategory')
                        ->name('table.category.store');
                    Route::put('/{category}', 'updateCategory')
                        ->name('table.category.update');
                    Route::delete('/{category}', 'destroyCategory')
                        ->name('table.category.destroy');
                });
            
                // Sütun route-ları
                Route::prefix('column')->group(function () {
                    Route::post('/', 'storeColumn')
                        ->name('table.column.store');
                    Route::put('/{column}', 'updateColumn')
                        ->name('table.column.update');
                    Route::delete('/{column}', 'destroyColumn')
                        ->name('table.column.destroy');
                });
            });
        });

        // Personal ayarları
        Route::get('/personal', [SettingsController::class, 'personal'])
            ->name('personal');

        // İstifadəçi İdarəetməsi (User Management)
        Route::resource('users', UserManagementController::class);
        Route::put('users/{user}/status', [UserManagementController::class, 'updateStatus'])
            ->name('users.status');
        Route::put('users/{user}/roles', [UserManagementController::class, 'updateRoles'])
            ->name('users.roles');

        // Məktəb İdarəetməsi (School Management)
        Route::resource('schools', SchoolManagementController::class);
        Route::put('schools/{school}/status', [SchoolManagementController::class, 'updateStatus'])
            ->name('schools.status');
        Route::post('schools/{school}/admin', [SchoolManagementController::class, 'assignAdmin'])
            ->name('schools.admin');

        // Sektor İdarəetməsi (Sector Management)
        Route::resource('sectors', SectorManagementController::class);
        Route::post('sectors/{sector}/schools', [SectorManagementController::class, 'addSchool'])
            ->name('sectors.schools');
        Route::post('sectors/{sector}/admin', [SectorManagementController::class, 'assignAdmin'])
            ->name('sectors.admin');

        // Region İdarəetməsi (Region Management)
        Route::resource('regions', RegionManagementController::class);
        Route::post('regions/{region}/sectors', [RegionManagementController::class, 'addSector'])
            ->name('regions.sectors');
    });

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('profile')->middleware('auth')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])
            ->name('profile');
        Route::put('/', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.password');
    });

    /*
    |--------------------------------------------------------------------------
    | API Documentation Routes
    |--------------------------------------------------------------------------
    */
    Route::get('/api-docs', function () {
        return view('pages.api-docs');
    })->middleware('role:super_admin')->name('api-docs');
});

/*
|--------------------------------------------------------------------------
| Debug Routes (Local Environment Only)
|--------------------------------------------------------------------------
*/
if (app()->environment('local')) {
    Route::prefix('debug')->group(function () {
        Route::get('/user', [DebugController::class, 'checkUser']);
        Route::get('/password', [DebugController::class, 'checkPassword']);
    });
}

/*
|--------------------------------------------------------------------------
| 404 Fallback Route
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    abort(404,'Səhifə tapılmadı');
});