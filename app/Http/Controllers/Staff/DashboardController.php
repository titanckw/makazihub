<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user  = auth()->user();
        $staff = Staff::where('user_id', $user->id)
            ->with('manager')
            ->firstOrFail();

        return view('staff.dashboard', compact('staff'));
    }
}
