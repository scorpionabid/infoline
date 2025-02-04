Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Auth olan istifadəçilər üçün
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // SuperAdmin routes
    Route::middleware(['role:superadmin'])->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    });

    // SectorAdmin routes
    Route::middleware(['role:sectoradmin'])->prefix('sector')->group(function () {
        Route::get('/dashboard', [SectorDashboardController::class, 'index'])->name('sector.dashboard');
    });

    // SchoolAdmin routes
    Route::middleware(['role:schooladmin'])->prefix('school')->group(function () {
        Route::get('/dashboard', [SchoolDashboardController::class, 'index'])->name('school.dashboard');
    });
});