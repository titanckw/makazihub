<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SettingsController;

// -------------------------------------------------------
// PUBLIC ROUTES
// -------------------------------------------------------
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// -------------------------------------------------------
// SUPERADMIN ROUTES
// -------------------------------------------------------
Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth', 'role:superadmin'])
    ->group(base_path('routes/superadmin.php'));

// -------------------------------------------------------
// MANAGER ROUTES
// -------------------------------------------------------
Route::prefix('manager')
    ->name('manager.')
    ->middleware(['auth', 'role:manager|superadmin'])
    ->group(base_path('routes/manager.php'));

// -------------------------------------------------------
// TENANT ROUTES
// -------------------------------------------------------
Route::prefix('tenant')
    ->name('tenant.')
    ->middleware(['auth', 'role:tenant'])
    ->group(base_path('routes/tenant.php'));


Route::post('/mpesa/callback', [\App\Http\Controllers\Manager\PaymentController::class, 'mpesaCallback']);

Route::middleware(['auth'])->post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationsController::class, 'markAsRead'])
    ->name('notifications.read');

Route::middleware(['auth'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile');
    Route::patch('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password');
});