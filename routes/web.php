<?php

use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\Petugas\DashboardController as PetugasDashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root Redirect
Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('petugas.dashboard');
    }

    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes (Common for Admin & Petugas)
Route::middleware(['auth', 'role'])->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Monitoring Operations accessible by authenticated users
    Route::get('/monitoring/create', [MonitoringController::class, 'create'])->name('monitoring.create');
    Route::post('/monitoring', [MonitoringController::class, 'store'])->name('monitoring.store');
    Route::get('/monitoring/{monitoring}', [MonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/monitoring/{monitoring}/edit', [MonitoringController::class, 'edit'])->name('monitoring.edit');
    Route::put('/monitoring/{monitoring}', [MonitoringController::class, 'update'])->name('monitoring.update');
});

// Admin Specific Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Master Data Area
    Route::resource('areas', AreaController::class);

    // User Management
    Route::resource('users', UserController::class);

    // Monitoring History (Admin Full View)
    Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::delete('/monitoring/{monitoring}', [MonitoringController::class, 'destroy'])->name('monitoring.destroy');

    // Reports
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/laporan/cetak', [ReportController::class, 'print'])->name('reports.print');
});

// Global alias for monitoring index so navigation links work seamlessly
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin-monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::delete('/admin-monitoring/{monitoring}', [MonitoringController::class, 'destroy'])->name('monitoring.destroy');
});

// Petugas Specific Routes
Route::middleware(['auth', 'role:petugas'])->prefix('petugas')->as('petugas.')->group(function () {
    Route::get('/dashboard', [PetugasDashboardController::class, 'index'])->name('dashboard');
    Route::get('/riwayat', [MonitoringController::class, 'myMonitoring'])->name('my-monitoring');
});
