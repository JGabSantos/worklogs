<?php

namespace App\Livewire\TimeEntries;

use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Services\TimeEntryService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
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

    public string $client_id = '';

    public string $status = 'draft';

    public string $description = '';

    public ?string $errorMessage = null;

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(?int $id = null): void
    {
        if ($id === null) {
            return;
        }

        if (! $this->canUpdate()) {
            abort(403, 'You do not have permission for this action.');
        }

        $this->fillFromEntry($id);
        $this->showModal = true;
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.time-entries.edit', [
            'canUpdate' => $this->canUpdate(),
            'activityTypes' => ActivityType::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'clients' => Client::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Modal actions
    // -------------------------------------------------------------------------

    #[On('open-edit-time-entry-modal')]
    public function openModal(int $id): void
    {
        if (! $this->canUpdate()) {
            abort(403, 'You do not have permission for this action.');
        }

        $this->fillFromEntry($id);
        $this->showModal = true;
        $this->errorMessage = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFormState();
        $this->resetValidation();
    }

    // -------------------------------------------------------------------------
    // Save
    // -------------------------------------------------------------------------

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

            session()->flash('success', 'Registro atualizado com sucesso.');

            $this->showModal = false;
            $this->resetFormState();

            $this->dispatch('time-entry-updated')->to(Index::class);
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    // -------------------------------------------------------------------------
    // Validation
    // -------------------------------------------------------------------------

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
            'date.date_format' => 'A data deve estar no formato dd/mm/aaaa.',
            'start_time.date_format' => 'A hora de início deve estar no formato hh:mm.',
            'end_time.date_format' => 'A hora de fim deve estar no formato hh:mm.',
        ];
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function canUpdate(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->hasPermission('time-entries.update.own');
    }

    private function fillFromEntry(int $id): void
    {
        $timeEntry = TimeEntry::query()
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

        return TimeEntry::query()
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
        $this->status = 'draft';
        $this->description = '';
        $this->errorMessage = null;
    }
}
