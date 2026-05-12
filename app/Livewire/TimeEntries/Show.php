<?php

namespace App\Livewire\TimeEntries;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public bool $showModal = false;

    public ?int $timeEntryId = null;

    public string $date = '';

    public string $location = '';

    public string $start_time = '';

    public string $end_time = '';

    public string $activity_type = '';

    public string $activity_type_id = '';

    public string $client = '';

    public string $client_id = '';

    public string $status = '';

    public string $description = '';

    public function mount(?int $id = null): void
    {
        if ($id === null) {
            return;
        }

        if (! $this->canView()) {
            abort(403, 'You do not have permission for this action.');
        }

        $this->fillFromEntry($id);
        $this->showModal = true;
    }

    #[On('open-show-time-entry-modal')]
    public function openModal(int $id): void
    {
        if (! $this->canView()) {
            abort(403, 'You do not have permission for this action.');
        }

        $this->fillFromEntry($id);
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFormState();
    }

    private function canView(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->hasPermission('time-entries.show.own');
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
        $this->activity_type = $timeEntry->activityType->name;
        $this->activity_type_id = (string) $timeEntry->activity_type_id;
        $this->client = $timeEntry->client->name;
        $this->client_id = (string) $timeEntry->client_id;
        $this->status = $timeEntry->status;
        $this->description = $timeEntry->description;

    }

    private function resetFormState(): void
    {
        $this->timeEntryId = null;
        $this->date = '';
        $this->location = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->activity_type = '';
        $this->activity_type_id = '';
        $this->client = '';
        $this->client_id = '';
        $this->status = '';
        $this->description = '';
    }

    public function render(): View
    {
        return view('livewire.time-entries.show', [
            'canView' => $this->canView(),
        ]);
    }
}
