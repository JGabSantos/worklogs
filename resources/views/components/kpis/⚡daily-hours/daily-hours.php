<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[On('time-entry-created')]
    #[On('time-entry-updated')]
    #[On('time-entry-deleted')]
    public function refreshKpi(): void {}

    #[Computed]
    public function todayHours(): string
    {
        $user = Auth::user();

        if (! $user) {
            return '0:00';
        }

        $todayMinutes = (int) $user->timeEntries()
            ->visible()
            ->whereDate('date', Carbon::today())
            ->sum('duration_minutes');

        return $this->formatMinutesAsHours($todayMinutes);
    }

    #[Computed]
    public function todayEntriesCount(): int
    {
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        return (int) $user->timeEntries()
            ->visible()
            ->whereDate('date', Carbon::today())
            ->count();
    }

    private function formatMinutesAsHours(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return sprintf('%d:%02d', $hours, $remainingMinutes);
    }
};
