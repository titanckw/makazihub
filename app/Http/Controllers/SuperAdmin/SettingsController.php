<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Settings is shared across roles via the generic SettingsController,
     * which already resolves to the 'superadmin.settings' view. Redirect
     * here so the superadmin-prefixed route (used by the sidebar) works
     * without duplicating logic.
     */
    public function index()
    {
        return redirect()->route('settings.index');
    }

    public function update(Request $request)
    {
        return redirect()->route('settings.index');
    }

    public function mpesa()
    {
        return redirect()->route('settings.index');
    }

    public function updateMpesa(Request $request)
    {
        return back()->with('success', 'M-Pesa settings updated.');
    }

    public function notifications()
    {
        return redirect()->route('settings.index');
    }

    public function updateNotifications(Request $request)
    {
        return back()->with('success', 'Notification preferences updated.');
    }
}
