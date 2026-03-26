<?php

namespace App\Livewire\TimeEntries;

use App\Models\TimeEntry;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Recents extends Component
{
    private const LATEST_LIMIT = 5;

    #[On('time-entry-created')]
    #[On('time-entry-updated')]
    #[On('time-entry-deleted')]
    public function refreshEntries(): void {}

    public function render(): View
    {
        $latestEntries = TimeEntry::query()
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
