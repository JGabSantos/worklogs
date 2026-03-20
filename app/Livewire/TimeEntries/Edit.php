<?php

namespace App\Livewire\TimeEntries;

use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Services\TimeEntryService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Edit extends Component
{
    public bool $showModal = false;

    public ?int $timeEntryId = null;

    public string $date = '';

    public string $location = '';

    public string $start_time = '';

    public string $end_time = '';

    public string $activity_type_id = '';

    public string $activityTypeSearch = '';

    public string $client_id = '';

    public string $clientSearch = '';

    public string $status = 'draft';

    public string $description = '';

    public ?string $errorMessage = null;

    public function mount(?int $id = null): void
    {
        if ($id === null) {
            return;
        }

        if (! $this->canUpdate()) {
            abort(403, 'You do not have permission for this action.');
        }

        $this->fillFromEntry($id);
        $this->syncSelectedAutocompleteLabel('client_id', 'clientSearch', Client::class);
        $this->syncSelectedAutocompleteLabel('activity_type_id', 'activityTypeSearch', ActivityType::class);
        $this->showModal = true;
    }

    #[On('open-edit-time-entry-modal')]
    public function openModal(int $id): void
    {
        if (! $this->canUpdate()) {
            abort(403, 'You do not have permission for this action.');
        }

        $this->fillFromEntry($id);
        $this->syncSelectedAutocompleteLabel('client_id', 'clientSearch', Client::class);
        $this->syncSelectedAutocompleteLabel('activity_type_id', 'activityTypeSearch', ActivityType::class);
        $this->showModal = true;
        $this->errorMessage = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFormState();
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'date' => ['required', 'date_format:d/m/Y'],
            'location' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i'],
            'activity_type_id' => ['required', 'numeric', 'exists:activity_types,id'],
            'client_id' => ['required', 'numeric', 'exists:clients,id'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:draft,active'],
        ];
    }

    protected function messages(): array
    {
        return [
            'date.date_format' => 'The date must be in the dd/mm/yyyy format.',
            'start_time.date_format' => 'The start time must be in the hh:mm format.',
            'end_time.date_format' => 'The end time must be in the hh:mm format.',
        ];
    }

    public function save(TimeEntryService $timeEntryService): void
    {
        $this->errorMessage = null;

        if (! $this->canUpdate()) {
            abort(403, 'You do not have permission for this action.');
        }

        $timeEntry = $this->resolveEditableTimeEntry();
        $validated = $this->validate();

        try {
            $timeEntryService->update($timeEntry, [
                ...$validated,
                'date' => Carbon::createFromFormat('d/m/Y', $validated['date'])->toDateString(),
                'start_time' => $validated['start_time'].':00',
                'end_time' => $validated['end_time'].':00',
            ], Auth::user());

            session()->flash('success', 'Entry updated successfully.');

            $this->showModal = false;
            $this->resetFormState();

            $this->dispatch('time-entry-created')->to(Index::class);
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    private function canUpdate(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->hasPermission('time-entries.update.own');
    }

    private function fillFromEntry(int $id): void
    {
        $timeEntry = Auth::user()
            ->timeEntries()
            ->visible()
            ->findOrFail($id);

        $this->timeEntryId = $timeEntry->id;
        $this->date = optional($timeEntry->date)->format('d/m/Y') ?? '';
        $this->location = $timeEntry->location;
        $this->start_time = Carbon::parse($timeEntry->start_time)->format('H:i');
        $this->end_time = Carbon::parse($timeEntry->end_time)->format('H:i');
        $this->activity_type_id = (string) $timeEntry->activity_type_id;
        $this->client_id = (string) $timeEntry->client_id;
        $this->status = $timeEntry->status;
        $this->description = $timeEntry->description;
    }

    private function resolveEditableTimeEntry(): TimeEntry
    {
        if ($this->timeEntryId === null) {
            abort(404, 'Time entry not found.');
        }

        return Auth::user()
            ->timeEntries()
            ->visible()
            ->findOrFail($this->timeEntryId);
    }

    private function resetFormState(): void
    {
        $this->timeEntryId = null;
        $this->date = '';
        $this->location = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->activity_type_id = '';
        $this->client_id = '';
        $this->activityTypeSearch = '';
        $this->clientSearch = '';
        $this->status = 'draft';
        $this->description = '';
        $this->errorMessage = null;
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
    }

    public function updatedActivityTypeId(): void
    {
        $this->syncSelectedAutocompleteLabel('activity_type_id', 'activityTypeSearch', ActivityType::class);
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
    // Autocomplete helpers
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
            fn (Builder $q) => $q->orderBy('sort_order')->orderBy('name'),
            fn (Builder $q) => $q->orderBy('name'),
        );

        return $query->limit(8)->get();
    }

    private function syncAutocompleteSearch(string $value, string $idProperty, string $searchProperty, string $modelClass): void
    {
        $value = trim($value);
        $this->{$searchProperty} = $value;

        if ($value === '') {
            $this->{$idProperty} = '';

            return;
        }

        $label = $this->resolveAutocompleteLabel($modelClass, $this->{$idProperty});

        if ($label === null || strcasecmp($label, $value) !== 0) {
            $this->{$idProperty} = '';
        }
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
    }

    private function clearAutocompleteSelection(string $idProperty, string $searchProperty): void
    {
        $this->{$idProperty} = '';
        $this->{$searchProperty} = '';
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

    public function render(): View
    {
        return view('livewire.time-entries.edit', [
            'canUpdate' => $this->canUpdate(),
            'activityTypes' => ActivityType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(),
            'clients' => Client::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'activityTypeSuggestions' => $this->getAutocompleteSuggestions(ActivityType::class, $this->activityTypeSearch),
            'clientSuggestions' => $this->getAutocompleteSuggestions(Client::class, $this->clientSearch),
        ]);
    }
}
