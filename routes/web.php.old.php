<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FabricController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisionProgressController;
use Illuminate\Support\Facades\Auth;


Route::get('/', function () {
    return redirect()->route('dashboard');
});


Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Master Customer
Route::resource('customers', CustomerController::class);
Route::get('api/customers/search', [CustomerController::class, 'searchApi'])->name('api.customers.search');

// Master Bahan Kain
Route::resource('master/fabrics', FabricController::class)->names('fabrics');
Route::get('api/fabrics/search', [FabricController::class, 'searchApi'])->name('api.fabrics.search');

// Kelola Akun Pengguna / User Divisi
Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

// Update Status Divisi (Checklist Antrean Operasional)
Route::middleware(['auth'])->group(function () {
    Route::get('/update-status', [DivisionProgressController::class, 'index'])->name('divisions.index');
    Route::post('/divisions/{order}/toggle', [DivisionProgressController::class, 'toggle'])->name('divisions.toggle');
    Route::post('/divisions/{order}/update-progress', [DivisionProgressController::class, 'updateProgress'])->name('divisions.update_progress');

    // dashboard & monitoring
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [DashboardController::class, 'summary'])->name('monitoring');
});



// Route Input Order Admin
// Route Sales Order
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');