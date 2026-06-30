<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $today = AttendanceLog::where('staff_id', $staff->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        $logs = AttendanceLog::where('staff_id', $staff->id)
            ->orderByDesc('date')
            ->paginate(15);

        return view('staff.attendance.index', compact('staff', 'today', 'logs'));
    }

    public function clockIn(Request $request)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $log = AttendanceLog::firstOrNew([
            'staff_id' => $staff->id,
            'date'     => now()->toDateString(),
        ]);

        if ($log->exists && $log->clock_in) {
            return back()->with('error', 'You have already clocked in today.');
        }

        $log->clock_in     = now();
        $log->clock_in_lat = $request->lat;
        $log->clock_in_lng = $request->lng;
        $log->status       = now()->format('H:i') > '09:15' ? 'late' : 'present';
        $log->save();

        return back()->with('success', 'Clocked in at ' . now()->format('H:i'));
    }

    public function clockOut(Request $request)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
        ]);

        $log = AttendanceLog::where('staff_id', $staff->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (!$log || !$log->clock_in) {
            return back()->with('error', 'You need to clock in before clocking out.');
        }

        if ($log->clock_out) {
            return back()->with('error', 'You have already clocked out today.');
        }

        $log->clock_out     = now();
        $log->clock_out_lat = $request->lat;
        $log->clock_out_lng = $request->lng;
        $log->save();

        return back()->with('success', 'Clocked out at ' . now()->format('H:i'));
    }
}
