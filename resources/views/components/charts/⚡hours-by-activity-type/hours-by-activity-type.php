<?php

use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    private const DEFAULT_PERIOD  = '30d';
    private const ALLOWED_PERIODS = ['today', '7d', '30d', '90d', '180d', '365d', 'all'];
    private const PERIOD_DAYS     = [
        'today' => 0,
        '7d'    => 6,
        '30d'   => 29,
        '90d'   => 89,
        '180d'  => 179,
        '365d'  => 364,
        'all'   => null,
    ];

    public string $period = self::DEFAULT_PERIOD;

    public array $chart = [
        'series'     => [],
        'categories' => [],
    ];

    public function mount(): void
    {
        $this->loadChart();
    }

    public function updatedPeriod(): void
    {
        if (! in_array($this->period, self::ALLOWED_PERIODS, strict: true)) {
            $this->period = self::DEFAULT_PERIOD;
        }

        $this->loadChart();

        $this->dispatchChartUpdated();
    }

    #[On('time-entry-created')]
    public function refreshChart(): void
    {
        $this->loadChart();

        $this->dispatchChartUpdated();
    }

    private function dispatchChartUpdated(): void
    {
        $this->dispatch(
            'hours-by-activity-type-chart-updated',
            series: $this->chart['series'],
            categories: $this->chart['categories'],
        );
    }

    private function loadChart(): void
    {
        $query = TimeEntry::query()
            ->visible()
            ->whereNotNull('time_entries.activity_type_id')
            ->join('activity_types', 'time_entries.activity_type_id', '=', 'activity_types.id')
            ->selectRaw('activity_types.name as activity_name, SUM(time_entries.duration_minutes) as total_minutes')
            ->groupBy('activity_types.id', 'activity_types.name')
            ->orderByDesc('total_minutes');

        $this->applyDateFilter($query);

        $rows = $query->get();

        $categories = $rows->pluck('activity_name')->toArray();
        $series     = $rows->pluck('total_minutes')
            ->map(fn(mixed $minutes): int => (int) $minutes)
            ->values()
            ->toArray();

        $this->chart = compact('series', 'categories');
    }

    private function applyDateFilter(Builder $query): void
    {
        $days = array_key_exists($this->period, self::PERIOD_DAYS)
            ? self::PERIOD_DAYS[$this->period]
            : self::PERIOD_DAYS[self::DEFAULT_PERIOD];

        if ($days === null) {
            return;
        }

        if ($days === 0) {
            $query->whereDate('time_entries.date', '=', now()->toDateString());

            return;
        }

        $query->whereDate('time_entries.date', '>=', now()->subDays($days)->startOfDay()->toDateString());
    }
};
