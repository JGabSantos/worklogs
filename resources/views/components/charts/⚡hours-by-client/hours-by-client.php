<?php

use App\Models\TimeEntry;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    private const DEFAULT_PERIOD  = 'today';
    private const ALLOWED_PERIODS = ['today', '7d', '30d', '90d', '180d', '365d', 'all'];
    private const TOP_LIMIT       = 10;
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
    #[On('time-entry-updated')]
    #[On('time-entry-deleted')]
    public function refreshChart(): void
    {
        $this->loadChart();

        $this->dispatchChartUpdated();
    }

    private function dispatchChartUpdated(): void
    {
        $this->dispatch(
            'time-by-client-chart-updated',
            series: $this->chart['series'],
            categories: $this->chart['categories'],
        );
    }

    private function loadChart(): void
    {
        $query = TimeEntry::query()
            ->visible()
            ->statusNotDraft()
            ->whereNotNull('time_entries.client_id')
            ->join('clients', 'time_entries.client_id', '=', 'clients.id')
            ->selectRaw('clients.name as client_name, SUM(time_entries.duration_minutes) as total_minutes')
            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('total_minutes');

        $this->applyDateFilter($query);

        $rows   = $query->get();
        $top    = $rows->take(self::TOP_LIMIT);
        $others = $rows->slice(self::TOP_LIMIT)->sum('total_minutes');

        $categories = $top->pluck('client_name')->toArray();
        $series     = $top->pluck('total_minutes')
            ->map(fn(mixed $m): int => (int) $m)
            ->values()
            ->toArray();

        if ($others > 0) {
            $categories[] = 'Outros';
            $series[]     = (int) $others;
        }

        $this->chart = compact('series', 'categories');
    }

    private function applyDateFilter(\Illuminate\Database\Eloquent\Builder $query): void
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
