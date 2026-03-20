<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    #[On('time-entry-created')]
    public function refreshKpi(): void {}

    #[Computed]
    public function monthlyHours(): string
    {
        $user = Auth::user();

        if (! $user) {
            return '0:00';
        }

        $currentMonthMinutes = (int) $user->timeEntries()
            ->visible()
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->sum('duration_minutes');

        return $this->formatMinutesAsHours($currentMonthMinutes);
    }

    #[Computed]
    public function monthlyEntriesCount(): int
    {
        $user = Auth::user();

        if (! $user) {
            return 0;
        }

        return (int) $user->timeEntries()
            ->visible()
            ->whereYear('date', Carbon::now()->year)
            ->whereMonth('date', Carbon::now()->month)
            ->count();
    }

    private function formatMinutesAsHours(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;

        return sprintf('%d:%02d', $hours, $remainingMinutes);
    }
};
