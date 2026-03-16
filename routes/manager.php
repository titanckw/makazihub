<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\PropertyController;
use App\Http\Controllers\Manager\UnitController;
use App\Http\Controllers\Manager\TenantController;
use App\Http\Controllers\Manager\LeaseController;
use App\Http\Controllers\Manager\InvoiceController;
use App\Http\Controllers\Manager\PaymentController;
use App\Http\Controllers\Manager\ReceiptController;
use App\Http\Controllers\Manager\NotificationController;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Properties
Route::resource('properties', PropertyController::class);

// Units (nested under properties)
Route::resource('properties.units', UnitController::class);

// Tenants
Route::resource('tenants', TenantController::class);
Route::get('tenants/{tenant}/ledger', [TenantController::class, 'ledger'])->name('tenants.ledger');

// Leases
Route::resource('leases', LeaseController::class);
Route::post('leases/{lease}/terminate', [LeaseController::class, 'terminate'])->name('leases.terminate');
Route::post('leases/{lease}/renew', [LeaseController::class, 'renew'])->name('leases.renew');

// Invoices
Route::resource('invoices', InvoiceController::class)->except(['edit', 'update', 'destroy']);
Route::post('invoices/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
Route::patch('invoices/{invoice}/mark-overdue', [InvoiceController::class, 'markOverdue'])->name('invoices.mark-overdue');
Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

// Payments
Route::post('payments/stk-push', [PaymentController::class, 'stkPush'])->name('payments.stk-push');
Route::resource('payments', PaymentController::class)->only(['index', 'show', 'create', 'store']);
Route::patch('payments/{payment}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
Route::patch('payments/{payment}/reverse', [PaymentController::class, 'reverse'])->name('payments.reverse');

// Receipts
Route::get('receipts', [ReceiptController::class, 'index'])->name('receipts.index');
Route::get('receipts/{receipt}/download', [ReceiptController::class, 'download'])->name('receipts.download');
Route::post('receipts/{receipt}/send', [ReceiptController::class, 'send'])->name('receipts.send');

// Notifications
Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('notifications/send-custom', [NotificationController::class, 'sendCustom'])->name('notifications.send-custom');
Route::post('notifications/send-all-overdue', [NotificationController::class, 'sendAllOverdue'])->name('notifications.send-all-overdue');
Route::post('notifications/invoice/{invoice}/send', [NotificationController::class, 'sendInvoice'])->name('notifications.send-invoice');
Route::post('notifications/invoice/{invoice}/send-overdue', [NotificationController::class, 'sendOverdue'])->name('notifications.send-overdue');
Route::get('notifications-log', [\App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.log');