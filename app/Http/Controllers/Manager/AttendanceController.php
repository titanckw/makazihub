<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $manager = Auth::user();
        $date = $request->date ?? now()->toDateString();

        $staffIds = Staff::where('manager_id', $manager->id)->pluck('id');

        $logs = AttendanceLog::with('staff.user')
            ->whereIn('staff_id', $staffIds)
            ->whereDate('date', $date)
            ->when($request->filled('staff_id'), fn($q) => $q->where('staff_id', $request->staff_id))
            ->orderBy('clock_in')
            ->get();

        $staffList = Staff::where('manager_id', $manager->id)->with('user')->where('status', 'active')->get();

        $summary = [
            'present' => $logs->whereIn('status', ['present', 'late'])->count(),
            'late'    => $logs->where('status', 'late')->count(),
            'total'   => $staffList->count(),
            'absent'  => $staffList->count() - $logs->count(),
        ];

        return view('manager.attendance.index', compact('logs', 'staffList', 'date', 'summary'));
    }
}
