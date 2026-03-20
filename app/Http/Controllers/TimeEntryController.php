<?php

namespace App\Http\Controllers;

use App\Services\TimeEntryService;
use Illuminate\Support\Facades\Auth;

class TimeEntryController extends Controller
{
    public function __construct(
        private TimeEntryService $timeEntryService
    ) {}

    public function index()
    {
        $timeEntrys = Auth::user()
            ->timeEntries()
            ->visible()
            ->with(['activityType', 'client'])
            ->latest('date')
            ->latest('start_time')
            ->paginate(10);

        return view('time-entries', compact('timeEntrys'));
    }

    public function destroy(int $id)
    {
        $timeEntry = Auth::user()
            ->timeEntries()
            ->visible()
            ->findOrFail($id);

        $this->timeEntryService->delete($timeEntry, Auth::user());

        return redirect()
            ->route('time-entries')
            ->with('success', 'Registo apagado com sucesso.');
    }
}
