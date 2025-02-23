<?php

use App\Http\Controllers\Web\WebAuthController;
use App\Http\Controllers\Web\Dashboard\DashboardController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\Settings\{
    SettingsController,
    Personal\ProfileController,
    Personal\RegionManagementController,
    Personal\SectorManagementController,
    Personal\SchoolController,
    Personal\SchoolManagementController,
    Personal\UserManagementController,
    Table\TableSettingsController,
    Table\DataTableSettingsController
};
use App\Http\Controllers\Settings\Personal\{
    DeadlineController,
    ReportController
};
use App\Http\Controllers\Web\{
    DebugController,
    WebExcelController,
};

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    
    // Login routes
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
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('index');

        // Super Admin Dashboard routes
        Route::middleware(['role:super-admin'])->group(function () {
            Route::get('/super-admin', [SuperAdminDashboardController::class, 'index'])->name('super-admin');
        });

        // Sector Admin Dashboard routes
        Route::middleware(['role:sector-admin'])->group(function () {
            Route::get('/sector-admin', [DashboardController::class, 'sectorAdmin'])->name('sector-admin');
        });

        // School Admin Dashboard routes
        Route::middleware(['role:school-admin'])->group(function () {
            Route::get('/school-admin', [DashboardController::class, 'schoolAdmin'])->name('school-admin');
        });
    });

    // Settings routes
    Route::prefix('settings')->name('settings.')->middleware(['auth'])->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        
        // System settings routes
        Route::prefix('system')->name('system.')->group(function () {
            Route::get('/', [SettingsController::class, 'system'])->name('index');
            
            // Notification settings
            Route::prefix('notifications')->name('notifications.')->group(function () {
                Route::get('/', [SettingsController::class, 'notifications'])->name('index');
                Route::post('/', [SettingsController::class, 'updateNotifications'])->name('update');
            });
            
            // Backup settings
            Route::prefix('backups')->name('backups.')->group(function () {
                Route::get('/', [SettingsController::class, 'backups'])->name('index');
                Route::post('/', [SettingsController::class, 'createBackup'])->name('create');
                Route::get('/{backup}', [SettingsController::class, 'downloadBackup'])->name('download');
                Route::delete('/{backup}', [SettingsController::class, 'deleteBackup'])->name('delete');
            });
            
            // Log settings
            Route::prefix('logs')->name('logs.')->group(function () {
                Route::get('/', [SettingsController::class, 'logs'])->name('index');
                Route::get('/{log}', [SettingsController::class, 'viewLog'])->name('view');
                Route::delete('/{log}', [SettingsController::class, 'deleteLog'])->name('delete');
            });
        });

        // Table settings
        Route::prefix('table')->name('table.')->group(function () {
            Route::get('/', [TableSettingsController::class, 'index'])->name('index');
            
            // Categories
            Route::prefix('category')->name('category.')->group(function () {
                Route::get('/', [TableSettingsController::class, 'index'])->name('index');
                Route::get('/list', [TableSettingsController::class, 'categories'])->name('list');
                Route::post('/', [TableSettingsController::class, 'storeCategory'])->name('store');
                Route::delete('/{id}', [TableSettingsController::class, 'destroyCategory'])->name('destroy');
            });
            
            // Columns
            Route::prefix('column')->name('column.')->group(function () {
                Route::post('/', [TableSettingsController::class, 'storeColumn'])->name('store');
                Route::get('/{id}', [TableSettingsController::class, 'showColumn'])->name('show');
                Route::put('/{id}', [TableSettingsController::class, 'updateColumn'])->name('update');
                Route::delete('/{id}', [TableSettingsController::class, 'destroyColumn'])->name('destroy');
            });
        });

        // Personal settings
        Route::prefix('personal')->name('personal.')->group(function () {
            Route::get('/', [SettingsController::class, 'personal'])->name('index');
            
            // Regions
            Route::prefix('regions')->name('regions.')->group(function () {
                Route::get('/', [RegionManagementController::class, 'index'])->name('index');
                Route::get('/data', [RegionManagementController::class, 'data'])->name('data');
                Route::get('/statistics', [RegionManagementController::class, 'statistics'])->name('statistics');
                Route::get('/create', [RegionManagementController::class, 'create'])->name('create');
                Route::post('/', [RegionManagementController::class, 'store'])->name('store');
                Route::get('/{region}/edit', [RegionManagementController::class, 'edit'])->name('edit');
                Route::put('/{region}', [RegionManagementController::class, 'update'])->name('update');
                Route::delete('/{region}', [RegionManagementController::class, 'destroy'])->name('destroy');
                Route::get('/{region}/assign-admin', [RegionManagementController::class, 'assignAdmin'])->name('assign-admin');
                Route::post('/{id}/restore', [RegionManagementController::class, 'restore'])->name('restore');
                Route::delete('/{id}/force-delete', [RegionManagementController::class, 'forceDelete'])->name('force-delete');
            });
            
            // Sectors
            Route::prefix('sectors')->name('sectors.')->group(function () {
                Route::get('/', [SectorManagementController::class, 'index'])->name('index');
                Route::get('/create', [SectorManagementController::class, 'create'])->name('create');
                Route::post('/', [SectorManagementController::class, 'store'])->name('store');
                Route::get('/{sector}/edit', [SectorManagementController::class, 'edit'])->name('edit');
                Route::put('/{sector}', [SectorManagementController::class, 'update'])->name('update');
                Route::delete('/{sector}', [SectorManagementController::class, 'destroy'])->name('destroy');
                Route::post('/{sector}/assign-admin', [SectorManagementController::class, 'assignAdmin'])->name('assign-admin');
            });

            // Schools
            Route::prefix('schools')->name('schools.')->group(function () {
                Route::get('/', [SchoolManagementController::class, 'index'])->name('index');
                Route::post('/', [SchoolManagementController::class, 'store'])->name('store');
                Route::get('/{school}', [SchoolManagementController::class, 'show'])->name('show');
                Route::get('/{school}/edit', [SchoolManagementController::class, 'edit'])->name('edit');
                Route::put('/{school}', [SchoolManagementController::class, 'update'])->name('update');
                Route::delete('/{school}', [SchoolManagementController::class, 'destroy'])->name('destroy');
                Route::get('/admins/available', [SchoolManagementController::class, 'getAvailableAdmins'])->name('admins.available');
                Route::post('/admins', [SchoolManagementController::class, 'createAdmin'])->name('admins.create');
                Route::post('/{school}/assign-admin', [SchoolManagementController::class, 'assignAdmin'])->name('assign-admin');
                Route::delete('/{school}/remove-admin', [SchoolManagementController::class, 'removeAdmin'])->name('remove-admin');
                
                // School data management
                Route::get('/{school}/data', [SchoolManagementController::class, 'showData'])->name('show.data');
                Route::post('/{school}/data', [SchoolManagementController::class, 'updateData'])->name('update.data');
            });

            // Users
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [UserManagementController::class, 'index'])->name('index');
                Route::get('/create', [UserManagementController::class, 'create'])->name('create');
                Route::post('/', [UserManagementController::class, 'store'])->name('store');
                Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
                Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
                Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
                Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
            });

            // Reports
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/schools', [ReportController::class, 'schools'])->name('schools');
                Route::get('/users', [ReportController::class, 'users'])->name('users');
                Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
            });

            // Deadlines
            Route::prefix('deadlines')->name('deadlines.')->group(function () {
                Route::get('/', [DeadlineController::class, 'index'])->name('index');
                Route::post('/', [DeadlineController::class, 'store'])->name('store');
                Route::put('/{deadline}', [DeadlineController::class, 'update'])->name('update');
                Route::delete('/{deadline}', [DeadlineController::class, 'destroy'])->name('destroy');
            });
        });

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/schools', [ReportController::class, 'schools'])->name('schools');
            Route::get('/sectors', [ReportController::class, 'sectors'])->name('sectors');
            Route::get('/regions', [ReportController::class, 'regions'])->name('regions');
            Route::post('/custom', [ReportController::class, 'custom'])->name('custom');
        });
    });

    // Profile routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // API docs
    Route::get('/api-docs', fn() => view('pages.api-docs'))
        ->middleware('role:superadmin')
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