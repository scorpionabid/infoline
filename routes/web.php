<?php

use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\API\V1\DashboardController; 
use App\Http\Controllers\API\V1\ExcelController;
use App\Http\Controllers\Web\{
    DebugController,
    WebExcelController,
};
use App\Http\Controllers\Settings\{
    SettingsController,
    Personal\ProfileController,  
    Personal\RegionManagementController,
    Personal\SectorManagementController,
    Personal\SchoolManagementController,
    Personal\UserManagementController,
    Table\TableSettingsController,
    Table\DataTableSettingsController,

};

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [WebAuthController::class, 'login'])
        ->middleware(['throttle:login']);

    // Password Reset Routes
    Route::get('forgot-password', [WebAuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');
    
    Route::post('forgot-password', [WebAuthController::class, 'sendPasswordResetLink'])
        ->name('password.email');
    
    Route::get('reset-password/{token}', [WebAuthController::class, 'showResetPasswordForm'])
        ->name('password.reset');
    
    Route::post('reset-password', [WebAuthController::class, 'resetPassword'])
        ->name('password.update');
});

// Auth routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

    // Dashboard routes
    Route::get('/dashboard', [\App\Http\Controllers\Web\Dashboard\DashboardController::class, 'index'])->name('dashboard');
    Route::middleware('role:super-admin')->get('/dashboard/super-admin', [\App\Http\Controllers\Web\Dashboard\DashboardController::class, 'superAdmin'])
        ->name('dashboard.super-admin');
    Route::middleware('role:sector-admin')->get('/dashboard/sector-admin', [\App\Http\Controllers\Web\Dashboard\DashboardController::class, 'sectorAdmin'])
        ->name('dashboard.sector-admin');
    Route::middleware('role:school-admin')->get('/dashboard/school-admin', [\App\Http\Controllers\Web\Dashboard\DashboardController::class, 'schoolAdmin'])
        ->name('dashboard.school-admin');

    // Settings routes
    Route::prefix('settings')->middleware('role:super-admin')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');

        Route::get('/table', [TableSettingsController::class, 'index'])->name('table');
        // Table management
        Route::prefix('table')->name('table.')->group(function () {
            Route::controller(TableSettingsController::class)->group(function () {
                Route::get('/', 'index')->name('index');

                Route::prefix('category')->name('category.')->group(function () {
                    Route::get('/', 'categories')->name('index');
                    Route::post('/', 'storeCategory')->name('store');
                    Route::put('/{category}', 'updateCategory')->name('update');
                    Route::delete('/{category}', 'destroyCategory')->name('destroy');
                });

                Route::prefix('column')->name('column.')->group(function () {
                    Route::get('/', 'columns')->name('index');
                    Route::post('/', 'storeColumn')->name('store');
                    Route::put('/{column}', 'updateColumn')->name('update');
                    Route::delete('/{column}', 'destroyColumn')->name('destroy');
                });
            });
        });

        // Personal management
        Route::prefix('personal')->name('personal.')->group(function () {
            Route::get('/', [SettingsController::class, 'personal'])->name('index');

            // User management
            Route::resource('users', UserManagementController::class);
            Route::put('users/{user}/status', [UserManagementController::class, 'updateStatus'])->name('users.status');
            Route::put('users/{user}/roles', [UserManagementController::class, 'updateRoles'])->name('users.roles');

            // Region management  
            Route::resource('regions', RegionManagementController::class);
            Route::post('regions/{region}/sectors', [RegionManagementController::class, 'addSector'])
                ->name('regions.sectors');
            Route::get('regions/{region}/edit', [RegionManagementController::class, 'edit'])
                ->name('regions.edit');
            Route::delete('regions/{region}', [RegionManagementController::class, 'destroy'])
                ->name('regions.destroy');
            Route::put('regions/{region}', [RegionManagementController::class, 'update'])
                ->name('regions.update');

            // Sector management
            Route::resource('sectors', SectorManagementController::class);
            Route::post('sectors/{sector}/schools', [SectorManagementController::class, 'addSchool'])
                ->name('sectors.schools');
            Route::post('sectors/{sector}/admin', [SectorManagementController::class, 'assignAdmin'])
                ->name('sectors.admin')
                ->middleware(['auth', 'role:superadmin']);
            Route::get('sectors/template', [SectorManagementController::class, 'downloadTemplate'])
                ->name('sectors.template');

            // School management
            Route::resource('schools', SchoolManagementController::class);
            Route::put('schools/{school}/status', [SchoolManagementController::class, 'updateStatus'])
                ->name('schools.status');
            Route::post('schools/{school}/admin', [SchoolManagementController::class, 'assignAdmin'])
                ->name('schools.admin');
            Route::get('schools/template', [SchoolManagementController::class, 'downloadTemplate'])
                ->name('schools.template');
        });
    });

    // Profile routes
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('profile');
        Route::put('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });

    // API docs
    Route::get('/api-docs', fn() => view('pages.api-docs'))
        ->middleware('role:super_admin')
        ->name('api-docs');
});

// Debug routes (local only)
if (app()->environment('local')) {
    Route::prefix('debug')->group(function () {
        Route::get('/user', [DebugController::class, 'checkUser']);
        Route::get('/password', [DebugController::class, 'checkPassword']);
    });
}

Route::fallback(fn() => abort(404, 'Səhifə tapılmadı'));