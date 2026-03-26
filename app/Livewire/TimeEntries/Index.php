<?php

namespace App\Livewire\TimeEntries;

use App\Models\ActivityType;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    private const ALLOWED_PER_PAGE = [10, 25, 50];

    private const ALLOWED_SORT_BY = ['date', 'duration_minutes'];

    private const ALLOWED_ORDER_BY = ['asc', 'desc'];

    private const DEFAULT_PER_PAGE = 10;

    private const DEFAULT_SORT_BY = 'date';

    private const DEFAULT_ORDER_BY = 'desc';

    #[Url(as: 'search', except: '')]
    public string $search = '';

    #[Url(as: 'date_from', except: '')]
    public string $dateFrom = '';

    public string $dateFromInput = '';

    #[Url(as: 'date_to', except: '')]
    public string $dateTo = '';

    public string $dateToInput = '';

    #[Url(as: 'status', except: '')]
    public string $status = '';

    #[Url(as: 'client_id', except: '')]
    public string $client_id = '';

    public string $clientSearch = '';

    #[Url(as: 'activity_type_id', except: '')]
    public string $activity_type_id = '';

    public string $activityTypeSearch = '';

    #[Url(as: 'duration_min', except: '')]
    public string $duration_min = '';

    #[Url(as: 'duration_max', except: '')]
    public string $duration_max = '';

    #[Url(as: 'sort_by', except: 'date')]
    public string $sort_by = self::DEFAULT_SORT_BY;

    #[Url(as: 'order_by', except: 'desc')]
    public string $orderBy = self::DEFAULT_ORDER_BY;

    #[Url(as: 'per_page', except: 10)]
    public int $perPage = self::DEFAULT_PER_PAGE;

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(): void
    {
        $this->normalizeDateFilter('dateFrom', 'dateFromInput');
        $this->normalizeDateFilter('dateTo', 'dateToInput');
        $this->syncSelectedAutocompleteLabel('client_id', 'clientSearch', Client::class);
        $this->syncSelectedAutocompleteLabel('activity_type_id', 'activityTypeSearch', ActivityType::class);
        $this->sanitizeSortAndPagination();
    }

    public function updated(string $property): void
    {
        $resetsPage = ['search', 'status', 'duration_min', 'duration_max', 'sort_by', 'perPage'];

        if (in_array($property, $resetsPage, strict: true)) {
            if ($property === 'perPage') {
                $this->sanitizeSortAndPagination();
            }

            $this->resetPage();
        }
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.time-entries.index', [
            'timeEntries' => $this->buildQuery()->paginate($this->perPage),
            'sortBy' => $this->sort_by,
            'orderBy' => $this->orderBy,
            'clientSuggestions' => $this->getAutocompleteSuggestions(Client::class, $this->clientSearch),
            'activityTypeSuggestions' => $this->getAutocompleteSuggestions(ActivityType::class, $this->activityTypeSearch),
            'hasAdvancedFiltersActive' => $this->hasAdvancedFiltersActive(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Sorting
    // -------------------------------------------------------------------------

    public function sort(string $column): void
    {
        $this->orderBy = ($this->sort_by === $column && $this->orderBy === 'asc')
            ? 'desc'
            : 'asc';

        $this->sort_by = $column;

        $this->resetPage();
    }

    // -------------------------------------------------------------------------
    // Filter actions
    // -------------------------------------------------------------------------

    public function clearFilters(): void
    {
        $this->search = '';
        $this->dateFrom = '';
        $this->dateFromInput = '';
        $this->dateTo = '';
        $this->dateToInput = '';
        $this->status = '';
        $this->client_id = '';
        $this->clientSearch = '';
        $this->activity_type_id = '';
        $this->activityTypeSearch = '';
        $this->duration_min = '';
        $this->duration_max = '';
        $this->sort_by = self::DEFAULT_SORT_BY;
        $this->orderBy = self::DEFAULT_ORDER_BY;
        $this->perPage = self::DEFAULT_PER_PAGE;

        $this->resetPage();
    }

    // -------------------------------------------------------------------------
    // Date filter watchers
    // -------------------------------------------------------------------------

    public function updatedDateFromInput(string $value): void
    {
        $this->syncDateFilter($value, 'dateFrom');
    }

    public function updatedDateToInput(string $value): void
    {
        $this->syncDateFilter($value, 'dateTo');
    }

    // -------------------------------------------------------------------------
    // Autocomplete watchers & actions
    // -------------------------------------------------------------------------

    public function updatedClientSearch(string $value): void
    {
        $this->syncAutocompleteSearch($value, 'client_id', 'clientSearch', Client::class);
    }

    public function updatedActivityTypeSearch(string $value): void
    {
        $this->syncAutocompleteSearch($value, 'activity_type_id', 'activityTypeSearch', ActivityType::class);
    }

    public function updatedClientId(): void
    {
        $this->syncSelectedAutocompleteLabel('client_id', 'clientSearch', Client::class);
        $this->resetPage();
    }

    public function updatedActivityTypeId(): void
    {
        $this->syncSelectedAutocompleteLabel('activity_type_id', 'activityTypeSearch', ActivityType::class);
        $this->resetPage();
    }

    public function selectClient(string $clientId): void
    {
        $this->setAutocompleteSelection('client_id', 'clientSearch', Client::class, $clientId);
    }

    public function clearClientSelection(): void
    {
        $this->clearAutocompleteSelection('client_id', 'clientSearch');
    }

    public function selectActivityType(string $activityTypeId): void
    {
        $this->setAutocompleteSelection('activity_type_id', 'activityTypeSearch', ActivityType::class, $activityTypeId);
    }

    public function clearActivityTypeSelection(): void
    {
        $this->clearAutocompleteSelection('activity_type_id', 'activityTypeSearch');
    }

    // -------------------------------------------------------------------------
    // Event listeners
    // -------------------------------------------------------------------------

    #[On('time-entry-created')]
    #[On('time-entry-updated')]
    #[On('time-entry-deleted')]
    public function refreshEntries(): void
    {
        $this->resetPage();
    }

    // -------------------------------------------------------------------------
    // Private — query building
    // -------------------------------------------------------------------------

    private function buildQuery(): Builder|Relation
    {
        $query = Auth::user()
            ->timeEntries()
            ->visible()
            ->with(['activityType', 'client']);

        $this->applySearchFilter($query);
        $this->applyDateFilters($query);
        $this->applyExactFilters($query);
        $this->applyDurationFilters($query);
        $this->applySorting($query);

        return $query;
    }

    private function applySearchFilter(Builder|Relation $query): void
    {
        if ($this->search === '') {
            return;
        }

        $term = trim($this->search);

        $query->where(function (Builder $q) use ($term) {
            $q->where('description', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%")
                ->orWhereHas('activityType', fn(Builder $r) => $r->where('name', 'like', "%{$term}%"))
                ->orWhereHas('client', fn(Builder $r) => $r->where('name', 'like', "%{$term}%"));
        });
    }

    private function applyDateFilters(Builder|Relation $query): void
    {
        if ($this->dateFrom !== '') {
            $this->applyDateBound($query, $this->dateFrom, '>=');
        }

        if ($this->dateTo !== '') {
            $this->applyDateBound($query, $this->dateTo, '<=');
        }
    }

    private function applyDateBound(Builder|Relation $query, string $date, string $operator): void
    {
        try {
            $parsed = Carbon::createFromFormat('m-d-Y', $date);
            $query->whereDate('date', $operator, $parsed->toDateString());
        } catch (\Throwable) {
            // Ignore invalid filter values.
        }
    }

    private function applyExactFilters(Builder|Relation $query): void
    {
        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->client_id !== '') {
            $query->where('client_id', $this->client_id);
        }

        if ($this->activity_type_id !== '') {
            $query->where('activity_type_id', $this->activity_type_id);
        }
    }

    private function applyDurationFilters(Builder|Relation $query): void
    {
        if ($this->duration_min !== '' && is_numeric($this->duration_min)) {
            $query->where('duration_minutes', '>=', (int) $this->duration_min);
        }

        if ($this->duration_max !== '' && is_numeric($this->duration_max)) {
            $query->where('duration_minutes', '<=', (int) $this->duration_max);
        }
    }

    private function applySorting(Builder|Relation $query): void
    {
        $dir = $this->orderBy;

        if ($this->sort_by === 'date') {
            $query->orderBy('date', $dir)->orderBy('start_time', $dir);
        } else {
            $query->orderBy('duration_minutes', $dir)
                ->orderBy('date', 'desc')
                ->orderBy('start_time', 'desc');
        }
    }

    // -------------------------------------------------------------------------
    // Private — autocomplete helpers
    // -------------------------------------------------------------------------

    private function getAutocompleteSuggestions(string $modelClass, string $searchTerm): Collection
    {
        $term = trim($searchTerm);

        $query = $modelClass::query()->where('is_active', true);

        if ($term !== '') {
            foreach (explode(' ', $term) as $word) {
                $query->where('name', 'like', "%{$word}%");
            }
        }

        $query->when(
            $modelClass === ActivityType::class,
            fn(Builder $q) => $q->orderBy('sort_order')->orderBy('name'),
            fn(Builder $q) => $q->orderBy('name'),
        );

        return $query->limit(8)->get();
    }

    private function syncAutocompleteSearch(string $value, string $idProperty, string $searchProperty, string $modelClass): void
    {
        $value = trim($value);
        $this->{$searchProperty} = $value;

        if ($value === '') {
            $this->{$idProperty} = '';
            $this->resetPage();

            return;
        }

        $label = $this->resolveAutocompleteLabel($modelClass, $this->{$idProperty});

        if ($label === null || strcasecmp($label, $value) !== 0) {
            $this->{$idProperty} = '';
        }

        $this->resetPage();
    }

    private function syncSelectedAutocompleteLabel(string $idProperty, string $searchProperty, string $modelClass): void
    {
        $label = $this->resolveAutocompleteLabel($modelClass, $this->{$idProperty});

        if ($label === null) {
            $this->{$idProperty} = '';
            $this->{$searchProperty} = '';

            return;
        }

        $this->{$searchProperty} = $label;
    }

    private function setAutocompleteSelection(string $idProperty, string $searchProperty, string $modelClass, string $selectedId): void
    {
        $label = $this->resolveAutocompleteLabel($modelClass, $selectedId);

        if ($label === null) {
            $this->clearAutocompleteSelection($idProperty, $searchProperty);

            return;
        }

        $this->{$idProperty} = $selectedId;
        $this->{$searchProperty} = $label;
        $this->resetPage();
    }

    private function clearAutocompleteSelection(string $idProperty, string $searchProperty): void
    {
        $this->{$idProperty} = '';
        $this->{$searchProperty} = '';
        $this->resetPage();
    }

    private function resolveAutocompleteLabel(string $modelClass, string $selectedId): ?string
    {
        if ($selectedId === '') {
            return null;
        }

        return $modelClass::query()
            ->whereKey($selectedId)
            ->where('is_active', true)
            ->value('name');
    }

    // -------------------------------------------------------------------------
    // Private — date helpers
    // -------------------------------------------------------------------------

    private function normalizeDateFilter(string $storageProperty, string $inputProperty): void
    {
        $stored = $this->{$storageProperty};

        if ($stored === '') {
            $this->{$inputProperty} = '';

            return;
        }

        foreach (['m-d-Y', 'd/m/Y'] as $format) {
            try {
                $parsed = Carbon::createFromFormat($format, $stored);
                $this->{$storageProperty} = $parsed->format('m-d-Y');
                $this->{$inputProperty} = $parsed->format('d/m/Y');

                return;
            } catch (\Throwable) {
                // Try next format.
            }
        }

        $this->{$storageProperty} = '';
        $this->{$inputProperty} = '';
    }

    private function syncDateFilter(string $value, string $storageProperty): void
    {
        $value = trim($value);

        if ($value === '') {
            $this->{$storageProperty} = '';
            $this->resetPage();

            return;
        }

        if (! preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
            return;
        }

        try {
            $this->{$storageProperty} = Carbon::createFromFormat('d/m/Y', $value)->format('m-d-Y');
            $this->resetPage();
        } catch (\Throwable) {
            // Ignore invalid date values.
        }
    }

    // -------------------------------------------------------------------------
    // Private — misc helpers
    // -------------------------------------------------------------------------

    private function sanitizeSortAndPagination(): void
    {
        if (! in_array($this->perPage, self::ALLOWED_PER_PAGE, strict: true)) {
            $this->perPage = self::DEFAULT_PER_PAGE;
        }

        if (! in_array($this->sort_by, self::ALLOWED_SORT_BY, strict: true)) {
            $this->sort_by = self::DEFAULT_SORT_BY;
        }

        if (! in_array($this->orderBy, self::ALLOWED_ORDER_BY, strict: true)) {
            $this->orderBy = self::DEFAULT_ORDER_BY;
        }
    }

    private function hasAdvancedFiltersActive(): bool
    {
        return $this->status !== ''
            || $this->client_id !== ''
            || $this->activity_type_id !== ''
            || $this->dateFrom !== ''
            || $this->dateTo !== ''
            || $this->duration_min !== ''
            || $this->duration_max !== ''
            || $this->sort_by !== self::DEFAULT_SORT_BY
            || $this->orderBy !== self::DEFAULT_ORDER_BY
            || $this->perPage !== self::DEFAULT_PER_PAGE;
    }
}
