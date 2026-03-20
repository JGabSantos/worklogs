<?php

namespace App\Livewire\TimeEntries;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Recents extends Component
{
    private const LATEST_LIMIT = 5;

    #[On('time-entry-created')]
    public function refreshEntries(): void {}

    public function render(): View
    {
        $latestEntries = Auth::user()
            ->timeEntries()
            ->visible()
            ->with(['activityType', 'client'])
            ->latest('date')
            ->latest('start_time')
            ->limit(self::LATEST_LIMIT)
            ->get();

        return view('livewire.time-entries.recents', [
            'latestEntries' => $latestEntries,
        ]);
    }
}
