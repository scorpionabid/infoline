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
   Table\CategoryController,
   Table\ColumnController,
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
use App\Http\Controllers\Web\Dashboard\SchoolAdmin\DashboardController as SchoolAdminDashboardController;
use App\Http\Controllers\Web\Dashboard\SectorAdmin\DashboardController as SectorAdminDashboardController;
use App\Http\Controllers\Web\Dashboard\DashboardController as MainDashboardController;

/**
* |--------------------------------------------------------------------------
* | Web Routes
* |--------------------------------------------------------------------------
* |
* | Bu fayl tətbiqin bütün veb route'larını təyin edir. Qonaqlar, autentifikasiya olunmuş
* | istifadəçilər və müxtəlif admin rolu olan istifadəçilər üçün route'lar təyin olunur.
*/

// Qonaq istifadəçilər üçün route'lar
Route::middleware('guest')->group(function () {
   // Ana səhifəni login'ə yönləndir
   Route::get('/', fn() => redirect()->route('login'));
   
   // Login route'ları
   Route::get('/login', [WebAuthController::class, 'showLoginForm'])->name('login');
   Route::post('/login', [WebAuthController::class, 'login'])
       ->middleware(['throttle:login']);

   // Şifrə sıfırlama route'ları
   Route::get('forgot-password', [WebAuthController::class, 'showForgotPasswordForm'])
       ->name('password.request');
   Route::post('forgot-password', [WebAuthController::class, 'sendPasswordResetLink'])
       ->name('password.email');
   Route::get('reset-password/{token}', [WebAuthController::class, 'showResetPasswordForm'])
       ->name('password.reset');
   Route::post('reset-password', [WebAuthController::class, 'resetPassword'])
       ->name('password.update');
});

// Autentifikasiya olunmuş istifadəçilər üçün route'lar
Route::middleware('auth')->group(function () {
   // Çıxış route'u
   Route::post('/logout', [WebAuthController::class, 'logout'])->name('logout');

   // Dashboard route'ları
   Route::prefix('dashboard')->name('dashboard.')->group(function () {
       // Ümumi dashboard
       Route::get('/', [DashboardController::class, 'index'])->name('index');

       // SuperAdmin dashboard
       Route::middleware(['role:super'])->group(function () {
           Route::get('/super-admin', [SuperAdminDashboardController::class, 'index'])->name('super-admin');
       });

       // Sektor admin dashboard
       Route::middleware(['role:sector'])->group(function () {
           Route::get('/sector-admin', [SectorAdminDashboardController::class, 'index'])->name('sector-admin');
       });

       // Məktəb admin dashboard
       Route::middleware(['role:school'])->group(function () {
           Route::get('/school-admin', [SchoolAdminDashboardController::class, 'index'])->name('school-admin');
       });
   });

   // Ayarlar route'ları - Yalnız superadmin üçün
   Route::prefix('settings')->name('settings.')->middleware(['role:super'])->group(function () {
       // Ayarlar ana səhifəsi
       Route::get('/', [SettingsController::class, 'index'])->name('index');
       
       // Sistem ayarları
       Route::prefix('system')->name('system.')->group(function () {
           Route::get('/', [SettingsController::class, 'system'])->name('index');
           
           // Bildiriş ayarları
           Route::prefix('notifications')->name('notifications.')->group(function () {
               Route::get('/', [SettingsController::class, 'notifications'])->name('index');
               Route::post('/', [SettingsController::class, 'updateNotifications'])->name('update');
           });
           
           // Yedekləmə ayarları
           Route::prefix('backups')->name('backups.')->group(function () {
               Route::get('/', [SettingsController::class, 'backups'])->name('index');
               Route::post('/', [SettingsController::class, 'createBackup'])->name('create');
               Route::get('/{backup}', [SettingsController::class, 'downloadBackup'])->name('download');
               Route::delete('/{backup}', [SettingsController::class, 'deleteBackup'])->name('delete');
           });
           
           // Log ayarları
           Route::prefix('logs')->name('logs.')->group(function () {
               Route::get('/', [SettingsController::class, 'logs'])->name('index');
               Route::get('/{log}', [SettingsController::class, 'viewLog'])->name('view');
               Route::delete('/{log}', [SettingsController::class, 'deleteLog'])->name('delete');
           });
       });

       // Cədvəl ayarları
    Route::prefix('table')->name('table.')->group(function () {
        // Əsas cədvəl ayarları
        Route::get('/', [TableSettingsController::class, 'index'])->name('index');
    
    // Kateqoriyalar
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [TableSettingsController::class, 'getAllCategories'])->name('all');
            Route::post('/', [TableSettingsController::class, 'storeCategory'])->name('store');
            Route::get('/{id}', [TableSettingsController::class, 'showCategory'])->name('show');
            Route::put('/{id}', [TableSettingsController::class, 'updateCategory'])->name('update');
            Route::delete('/{id}', [TableSettingsController::class, 'destroyCategory'])->name('destroy');
            Route::patch('/{id}/status', [CategoryController::class, 'updateStatus'])->name('status');
            Route::get('/{id}/assignments', [TableSettingsController::class, 'getCategoryAssignments'])->name('assignments');
            Route::post('/{id}/clone', [TableSettingsController::class, 'cloneCategory'])->name('clone');
        });
    
    // Sütunlar
        Route::prefix('columns')->name('columns.')->group(function () {
            Route::get('/', [ColumnController::class, 'index'])->name('index');
            Route::get('/category/{category}', [ColumnController::class, 'getColumnsByCategory'])->name('by-category');
            Route::post('/', [ColumnController::class, 'store'])->name('store');
            Route::get('/{id}', [ColumnController::class, 'show'])->name('show');
            Route::put('/{id}', [ColumnController::class, 'update'])->name('update');
            Route::delete('/{id}', [ColumnController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/status', [ColumnController::class, 'updateStatus'])->name('status');
            Route::patch('/{id}/deadline', [ColumnController::class, 'updateDeadline'])->name('deadline');
            Route::patch('/{id}/limit', [ColumnController::class, 'updateLimit'])->name('limit');
            Route::post('/category/{category}/order', [ColumnController::class, 'updateOrder'])->name('order');
            
            // Sütun seçimləri
            Route::prefix('{id}/choices')->name('choices.')->group(function () {
                Route::get('/', [ColumnController::class, 'getChoices'])->name('index');
                Route::post('/', [ColumnController::class, 'storeChoice'])->name('store');
                Route::put('/{choice}', [ColumnController::class, 'updateChoice'])->name('update');
                Route::delete('/{choice}', [ColumnController::class, 'destroyChoice'])->name('destroy');
            });
        });
    });
    
        // Son tarixlər
        Route::prefix('deadlines')->name('deadlines.')->group(function () {
            Route::get('/', [DeadlineController::class, 'index'])->name('index');
            Route::post('/', [DeadlineController::class, 'store'])->name('store');
            Route::put('/{deadline}', [DeadlineController::class, 'update'])->name('update');
            Route::delete('/{deadline}', [DeadlineController::class, 'destroy'])->name('destroy');
        });

       // Personal ayarları
       Route::prefix('personal')->name('personal.')->group(function () {
           Route::get('/', [SettingsController::class, 'personal'])->name('index');
           
           // Region əməliyyatları
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
           
           // Sektor əməliyyatları
           Route::prefix('sectors')->name('sectors.')->group(function () {
               Route::get('/', [SectorManagementController::class, 'index'])->name('index');
               Route::get('/create', [SectorManagementController::class, 'create'])->name('create');
               Route::post('/', [SectorManagementController::class, 'store'])->name('store');
               Route::get('/{sector}/edit', [SectorManagementController::class, 'edit'])->name('edit');
               Route::put('/{sector}', [SectorManagementController::class, 'update'])->name('update');
               Route::delete('/{sector}', [SectorManagementController::class, 'destroy'])->name('destroy');
               
               // Sektor admin əməliyyatları
               Route::prefix('{sector}/admin')->name('admin.')->group(function () {
                   Route::get('/create', [SectorManagementController::class, 'createAdminForm'])->name('create');
                   Route::post('/', [SectorManagementController::class, 'storeAdmin'])->name('store');
                   Route::delete('/', [SectorManagementController::class, 'removeAdmin'])->name('remove');
               });
           });

           // Məktəb əməliyyatları
           Route::prefix('schools')->name('schools.')->group(function () {
               Route::get('/', [SchoolController::class, 'index'])->name('index');
               Route::get('/create', [SchoolController::class, 'create'])->name('create');
               Route::post('/', [SchoolController::class, 'store'])->name('store');
               Route::get('/{school}', [SchoolController::class, 'show'])->name('show');
               Route::get('/{school}/edit', [SchoolController::class, 'edit'])->name('edit');
               Route::put('/{school}', [SchoolController::class, 'update'])->name('update');
               Route::delete('/{school}', [SchoolController::class, 'destroy'])->name('destroy');
               
               // Məktəb admin əməliyyatları
               Route::prefix('{school}/admin')->name('admin.')->group(function () {
                   Route::get('/create', [SchoolController::class, 'createAdmin'])->name('create');
                   Route::post('/', [SchoolController::class, 'storeAdmin'])->name('store');
                   Route::post('/assign', [SchoolController::class, 'assignAdmin'])->name('assign');
                   Route::delete('/remove', [SchoolController::class, 'removeAdmin'])->name('remove');
               });
               
               // Məktəb məlumatları əməliyyatları
               Route::get('/{school}/data', [SchoolManagementController::class, 'showData'])->name('show.data');
               Route::post('/{school}/data', [SchoolManagementController::class, 'updateData'])->name('update.data');
               
               // Məktəb admin əməliyyatları (əlavə)
               Route::get('/admins/{admin}', [SchoolManagementController::class, 'getAdmin'])->name('admins.show');
               Route::put('/{school}/admins/{admin}', [SchoolManagementController::class, 'updateAdmin'])->name('admins.update');
               Route::post('/{school}/admins', [SchoolManagementController::class, 'createAdmin'])->name('admins.store');
           });

           // İstifadəçi əməliyyatları
           Route::prefix('users')->name('users.')->group(function () {
               Route::get('/', [UserManagementController::class, 'index'])->name('index');
               Route::get('/create', [UserManagementController::class, 'create'])->name('create');
               Route::post('/', [UserManagementController::class, 'store'])->name('store');
               Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit');
               Route::put('/{user}', [UserManagementController::class, 'update'])->name('update');
               Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy');
               Route::patch('/{user}/toggle-status', [UserManagementController::class, 'toggleStatus'])->name('toggle-status');
           });

           // Hesabat əməliyyatları
           Route::prefix('reports')->name('reports.')->group(function () {
               Route::get('/schools', [ReportController::class, 'schools'])->name('schools');
               Route::get('/users', [ReportController::class, 'users'])->name('users');
               Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
           });

           // Son tarix əməliyyatları
           Route::prefix('deadlines')->name('deadlines.')->group(function () {
               Route::get('/', [DeadlineController::class, 'index'])->name('index');
               Route::post('/', [DeadlineController::class, 'store'])->name('store');
               Route::put('/{deadline}', [DeadlineController::class, 'update'])->name('update');
               Route::delete('/{deadline}', [DeadlineController::class, 'destroy'])->name('destroy');
           });
       });

       // Hesabat route'ları
       Route::prefix('reports')->name('reports.')->group(function () {
           Route::get('/schools', [ReportController::class, 'schools'])->name('schools');
           Route::get('/sectors', [ReportController::class, 'sectors'])->name('sectors');
           Route::get('/regions', [ReportController::class, 'regions'])->name('regions');
           Route::post('/custom', [ReportController::class, 'custom'])->name('custom');
       });
   });

   // Profil route'ları
   Route::prefix('profile')->name('profile.')->group(function () {
       Route::get('/', [ProfileController::class, 'show'])->name('index');
       Route::put('/', [ProfileController::class, 'update'])->name('update');
       Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
   });

   // API sənədləri - sadəcə superadmin üçün
   Route::get('/api-docs', fn() => view('pages.api-docs'))
       ->middleware('role:superadmin')
       ->name('api-docs');
});

// Debug route'ları (sadəcə local mühitdə)
if (app()->environment('local')) {
   Route::prefix('debug')->group(function () {
       Route::get('/user', [DebugController::class, 'checkUser']);
       Route::get('/password', [DebugController::class, 'checkPassword']);
   });
}

// Əgər route tapılmasa 404 xətası qaytar
Route::fallback(fn() => abort(404, 'Səhifə tapılmadı'));

// Klient tərəfindən olan xətaları log etmək üçün
Route::post('/log/client-error', [
   'uses' => 'LoggingController@logClientError',
   'middleware' => ['web']
]);
// routes/web.php
Route::post('/export-excel', [ReportController::class, 'exportExcel'])
     ->name('export.excel')
     ->middleware('role:super,sector');

// routes/api.php (yeni yaradılmalı)
Route::prefix('v1')->group(function () {
    Route::apiResource('notifications', NotificationController::class);
});