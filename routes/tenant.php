<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\InvoiceController;
use App\Http\Controllers\Tenant\LeaseController;
use App\Http\Controllers\Tenant\PaymentController;
use App\Http\Controllers\Tenant\ReceiptController;
use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\MaintenanceController;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Lease
Route::get('lease', [LeaseController::class, 'show'])->name('lease.show');

// Invoices
Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

// Payments
Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

// Receipts
Route::get('receipts/{receipt}/download', [ReceiptController::class, 'download'])->name('receipts.download');

// Profile
Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::patch('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

Route::post('invoices/{invoice}/stk-push', [InvoiceController::class, 'stkPush'])->name('payments.stk-push');


// Maintenance Requests
Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
Route::get('maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
Route::post('maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
Route::get('maintenance/{maintenance}', [MaintenanceController::class, 'show'])->name('maintenance.show');
Route::get('notifications', [\App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.index');