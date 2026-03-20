<?php

namespace App\Livewire\TimeEntries;

use App\Models\TimeEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public int $timeEntryId;

    public TimeEntry $timeEntry;

    public function mount(int $id): void
    {
        $this->timeEntry = Auth::user()
            ->timeEntries()
            ->visible()
            ->with(['activityType', 'client'])
            ->findOrFail($id);

        $this->timeEntryId = $this->timeEntry->id;
    }

    public function render(): View
    {
        return view('livewire.time-entries.show', [
            'timeEntry' => $this->timeEntry,
        ]);
    }
}
