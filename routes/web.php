<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/profile', [DashboardController::class, 'adminProfile'])->name('profile');
        Route::get('/bookings', [DashboardController::class, 'adminBookings'])->name('bookings.index');
        Route::get('/assignments', [DashboardController::class, 'adminAssignments'])->name('assignments');
        Route::get('/inventory', [DashboardController::class, 'adminInventory'])->name('inventory');
        Route::get('/mechanics', [DashboardController::class, 'adminMechanics'])->name('mechanics.index');
        Route::get('/customers', [DashboardController::class, 'adminCustomers'])->name('customers.index');
        Route::get('/reports', [DashboardController::class, 'adminReports'])->name('reports');
        Route::get('/audit-logs', [DashboardController::class, 'adminAuditLogs'])->name('audit');

        Route::post('/bookings', [DashboardController::class, 'storeBooking'])->name('bookings.store');
        Route::put('/bookings/{booking}', [DashboardController::class, 'updateBooking'])->name('bookings.update');
        Route::patch('/bookings/{booking}/status', [DashboardController::class, 'updateBookingStatus'])->name('bookings.status');
        Route::patch('/bookings/{booking}/assign', [DashboardController::class, 'assignMechanic'])->name('bookings.assign');
        Route::delete('/bookings/{booking}', [DashboardController::class, 'deleteBooking'])->name('bookings.destroy');

        Route::post('/parts', [DashboardController::class, 'storePart'])->name('parts.store');
        Route::put('/parts/{part}', [DashboardController::class, 'updatePart'])->name('parts.update');
        Route::delete('/parts/{part}', [DashboardController::class, 'deletePart'])->name('parts.destroy');

        Route::post('/mechanics', [DashboardController::class, 'storeMechanic'])->name('mechanics.store');
        Route::put('/mechanics/{mechanic}', [DashboardController::class, 'updateMechanic'])->name('mechanics.update');
        Route::delete('/mechanics/{mechanic}', [DashboardController::class, 'deleteMechanic'])->name('mechanics.destroy');

        Route::put('/customers/{customer}', [DashboardController::class, 'updateCustomer'])->name('customers.update');
        Route::delete('/customers/{customer}', [DashboardController::class, 'deleteCustomer'])->name('customers.destroy');
    });

    Route::middleware('role:mechanic')->prefix('mechanic')->name('mechanic.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'mechanic'])->name('dashboard');
        Route::get('/repairs', [DashboardController::class, 'mechanicRepairs'])->name('repairs.index');
        Route::get('/performance', [DashboardController::class, 'mechanicPerformance'])->name('performance');
        Route::patch('/repairs/{booking}/work', [DashboardController::class, 'updateMechanicRepair'])->name('repairs.work');
    });

    Route::get('/customer/dashboard', [DashboardController::class, 'customer'])
        ->middleware('role:customer')
        ->name('customer.dashboard');
});
