<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FabricController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisionProgressController;
use Illuminate\Support\Facades\Route;

// Guest Routes (Login)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Logout Route
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (Harus Login)
Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard & Monitoring
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [DashboardController::class, 'summary'])->name('monitoring');

    // Master Customer
    Route::resource('customers', CustomerController::class);
    Route::get('api/customers/search', [CustomerController::class, 'searchApi'])->name('api.customers.search');

    // Master Bahan Kain
    Route::resource('master/fabrics', FabricController::class)->names('fabrics');
    Route::get('api/fabrics/search', [FabricController::class, 'searchApi'])->name('api.fabrics.search');

    // Kelola Akun Pengguna / User Divisi
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    // Checklist Antrean Divisi
    Route::get('/update-status', [DivisionProgressController::class, 'index'])->name('divisions.index');
    Route::post('/divisions/{order}/toggle', [DivisionProgressController::class, 'toggle'])->name('divisions.toggle');
    Route::post('/divisions/{order}/update-progress', [DivisionProgressController::class, 'updateProgress'])->name('divisions.update_progress');

    // final status by admin, pesanan sudah diambil customer
    Route::post('/orders/{order}/toggle-complete', [DivisionProgressController::class, 'toggleComplete'])->name('orders.toggle_complete');

    // Sales Orders
    Route::resource('orders', OrderController::class);

    // API data monitoring progress
    Route::get('/api/monitoring-data', [DashboardController::class, 'getMonitoringData'])->name('api.monitoring_data');
});