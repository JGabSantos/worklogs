<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $todayMinutes = $user->timeEntries()
            ->visible()
            ->whereDate('date', $today)
            ->sum('duration_minutes');

        $weekMinutes = $user->timeEntries()
            ->visible()
            ->whereBetween('date', [$startOfWeek->toDateString(), $endOfWeek->toDateString()])
            ->sum('duration_minutes');

        $latestEntries = $user->timeEntries()
            ->visible()
            ->with(['activityType', 'client'])
            ->latest('date')
            ->latest('start_time')
            ->take(5)
            ->get();

        return view('dashboard', [
            'todayHours' => round($todayMinutes / 60, 2),
            'weekHours' => round($weekMinutes / 60, 2),
            'latestEntries' => $latestEntries,
        ]);
    }
}
