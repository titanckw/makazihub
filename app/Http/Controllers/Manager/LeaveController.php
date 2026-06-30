<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use App\Models\NotificationLog;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $manager = Auth::user();
        $staffIds = Staff::where('manager_id', $manager->id)->pluck('id');

        $requests = LeaveRequest::with('staff.user')
            ->whereIn('staff_id', $staffIds)
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $balances = Staff::where('manager_id', $manager->id)
            ->where('status', 'active')
            ->with(['user', 'leaveBalances' => fn($q) => $q->where('year', now()->year)])
            ->get();

        $pendingCount = LeaveRequest::whereIn('staff_id', $staffIds)->where('status', 'pending')->count();

        return view('manager.leave.index', compact('requests', 'balances', 'pendingCount'));
    }

    public function review(Request $request, LeaveRequest $leave)
    {
        $manager = Auth::user();
        abort_unless($leave->staff->manager_id === $manager->id, 403);

        $validated = $request->validate([
            'decision' => 'required|in:approved,rejected',
            'comment'  => 'nullable|string|max:1000',
        ]);

        $leave->update([
            'status'          => $validated['decision'],
            'manager_comment' => $validated['comment'] ?? null,
            'reviewed_by'     => $manager->id,
            'reviewed_at'     => now(),
        ]);

        if ($validated['decision'] === 'approved' && $leave->type === 'annual') {
            $balance = $leave->staff->currentLeaveBalance();
            $balance->increment('used_days', $leave->days);
        }

        NotificationLog::notify(
            $leave->staff->user_id,
            'leave_' . $validated['decision'],
            'Leave Request ' . ucfirst($validated['decision']),
            "Your {$leave->type_label} request ({$leave->start_date->format('M d')} - {$leave->end_date->format('M d, Y')}) was {$validated['decision']}."
        );

        return back()->with('success', 'Leave request ' . $validated['decision'] . '.');
    }
}
