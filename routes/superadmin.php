<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\SuperAdmin\PropertyController;
use App\Http\Controllers\SuperAdmin\ReportController;
use App\Http\Controllers\SuperAdmin\SettingsController;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Users Management
Route::resource('users', UserController::class);
Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

// Properties (SuperAdmin can view all)
Route::resource('properties', PropertyController::class)->only(['index', 'show']);

// Reports
Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
Route::get('reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');

// Settings
Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');
Route::get('settings/mpesa', [SettingsController::class, 'mpesa'])->name('settings.mpesa');
Route::post('settings/mpesa', [SettingsController::class, 'updateMpesa'])->name('settings.mpesa.update');
Route::get('settings/notifications', [SettingsController::class, 'notifications'])->name('settings.notifications');
Route::post('settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications.update');

// Notifications log (shared controller)
Route::get('notifications-log', [\App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.log');
