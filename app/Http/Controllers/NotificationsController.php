<?php
// app/Http/Controllers/NotificationsController.php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationsController extends Controller
{
    public function index(Request $request): View
    {
        $query = NotificationLog::where('user_id', auth()->id())
            ->when($request->channel, fn($q, $v) => $q->where('channel', $v))
            ->when($request->status,  fn($q, $v) => $q->where('status', $v))
            ->when($request->type,    fn($q, $v) => $q->where('type', $v))
            ->latest();

        $notifications = $query->paginate(15)->withQueryString();

        $totalSent   = NotificationLog::where('user_id', auth()->id())->where('status', 'sent')->count();
        $totalFailed = NotificationLog::where('user_id', auth()->id())->where('status', 'failed')->count();
        $smsSent     = NotificationLog::where('user_id', auth()->id())->where('channel', 'sms')->where('status', 'sent')->count();
        $emailSent   = NotificationLog::where('user_id', auth()->id())->where('channel', 'email')->where('status', 'sent')->count();

        // Determine which layout to use based on role
        $layout = match(true) {
            auth()->user()->hasRole('superadmin') => 'layouts.app',
            auth()->user()->hasRole('manager')    => 'layouts.app',
            default                               => 'layouts.tenant',
        };

        return view('shared.notifications', compact(
            'notifications',
            'totalSent',
            'totalFailed',
            'smsSent',
            'emailSent',
            'layout'
        ));
    }

    public function markAsRead(Request $request, NotificationLog $notification)
    {
        abort_unless($notification->user_id === auth()->id(), 403);

        if (!$notification->read_at) {
            $notification->update(['read_at' => now()]);
        }

        return response()->json(['success' => true, 'read_at' => $notification->read_at]);
    }
}
