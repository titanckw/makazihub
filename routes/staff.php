<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\LeaveController;
use App\Http\Controllers\Staff\ProfileController;
use App\Http\Controllers\ChatController;

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Attendance
Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index');
Route::post('attendance/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
Route::post('attendance/clock-out', [AttendanceController::class, 'clockOut'])->name('attendance.clock-out');

// Leave Management
Route::get('leave', [LeaveController::class, 'index'])->name('leave.index');
Route::post('leave', [LeaveController::class, 'store'])->name('leave.store');
Route::post('leave/{leave}/cancel', [LeaveController::class, 'cancel'])->name('leave.cancel');

// Profile
Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::post('profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');

// Chat
Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
Route::post('chat', [ChatController::class, 'store'])->name('chat.store');
Route::get('chat/poll', [ChatController::class, 'poll'])->name('chat.poll');

// Notifications
Route::get('notifications', [\App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.index');

