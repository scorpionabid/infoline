<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CategoryController;
use App\Http\Controllers\API\V1\DataValueController;
use App\Http\Controllers\API\V1\ExcelController;
use App\Http\Controllers\API\V1\ImportController;
use App\Http\Controllers\API\V1\PermissionController;
use App\Http\Controllers\API\V1\RegionController;
use App\Http\Controllers\API\V1\RoleController;
use App\Http\Controllers\API\V1\SchoolController;
use App\Http\Controllers\API\V1\SectorController;
use App\Http\Controllers\API\V1\NotificationController;
use App\Http\Controllers\API\V1\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/user', [AuthController::class, 'user']);

        // Region routes
        Route::apiResource('regions', RegionController::class);

        // Sector routes
        Route::apiResource('sectors', SectorController::class);
        Route::get('regions/{region}/sectors', [SectorController::class, 'index']);

        // School routes
        Route::apiResource('schools', SchoolController::class);
        Route::get('sectors/{sector}/schools', [SchoolController::class, 'index']);
        Route::get('schools/{school}/admins', [SchoolController::class, 'admins']);

        // Permission routes
        Route::apiResource('permissions', PermissionController::class);
        Route::post('permissions/{permission}/roles', [PermissionController::class, 'assignRole']);
        Route::delete('permissions/{permission}/roles/{role}', [PermissionController::class, 'revokeRole']);

        // Role routes
        Route::apiResource('roles', RoleController::class);

        // Category routes
        Route::apiResource('categories', CategoryController::class);

        // DataValue routes
        Route::post('data-values/bulk-update', [DataValueController::class, 'bulkUpdate']);
        Route::apiResource('data-values', DataValueController::class);
        Route::post('data-values/{dataValue}/submit', [DataValueController::class, 'submit']);
        Route::post('data-values/{dataValue}/approve', [DataValueController::class, 'approve']);
        Route::post('data-values/{dataValue}/reject', [DataValueController::class, 'reject']);

        // Excel routes
        Route::controller(ExcelController::class)->group(function () {
            Route::get('excel/export', 'export');
        });

        // Import routes
        Route::controller(ImportController::class)->group(function () {
            Route::post('excel/import', 'import');
        });

        // Notification routes
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead']);

        // Dashboard routes
        Route::get('dashboard/statistics', [DashboardController::class, 'statistics']);
        Route::get('dashboard/region-statistics', [DashboardController::class, 'regionStatistics']);
        Route::get('dashboard/school-statistics', [DashboardController::class, 'schoolStatistics']);
        Route::get('dashboard/data-submission-stats', [DashboardController::class, 'dataSubmissionStats']);
    });
});