<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\NotificationLog;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();
        $balance = $staff->currentLeaveBalance();
        $requests = $staff->leaveRequests()->latest()->paginate(10);

        return view('staff.leave.index', compact('staff', 'balance', 'requests'));
    }

    public function store(Request $request)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'type'       => 'required|in:annual,sick,unpaid,compassionate',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'nullable|string|max:1000',
        ]);

        $days = \Carbon\Carbon::parse($validated['start_date'])
            ->diffInDaysFiltered(fn($date) => !$date->isWeekend(), \Carbon\Carbon::parse($validated['end_date'])) + 1;

        $balance = $staff->currentLeaveBalance();
        if ($validated['type'] === 'annual' && $days > $balance->remaining_days) {
            return back()->with('error', "Insufficient leave balance. You have {$balance->remaining_days} day(s) remaining.");
        }

        $leave = LeaveRequest::create([
            'staff_id'   => $staff->id,
            'type'       => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date'   => $validated['end_date'],
            'days'       => $days,
            'reason'     => $validated['reason'],
            'status'     => 'pending',
        ]);

        NotificationLog::notify(
            $staff->manager_id,
            'leave_request',
            'New Leave Request',
            "{$staff->user->name} requested {$days} day(s) of {$leave->type_label} starting {$leave->start_date->format('M d, Y')}."
        );

        return redirect()->route('staff.leave.index')->with('success', 'Leave request submitted.');
    }

    public function cancel(LeaveRequest $leave)
    {
        $staff = Staff::where('user_id', Auth::id())->firstOrFail();
        abort_unless($leave->staff_id === $staff->id, 403);
        abort_unless($leave->status === 'pending', 422, 'Only pending requests can be cancelled.');

        $leave->update(['status' => 'cancelled']);

        return back()->with('success', 'Leave request cancelled.');
    }
}
