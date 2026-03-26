<?php

namespace App\Livewire\TimeEntries;

use App\Services\TimeEntryService;
use App\Models\TimeEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Delete extends Component
{
    private const DEFAULT_BUTTON_LABEL = 'Delete';

    private const DEFAULT_BUTTON_VARIANT = 'danger';

    private const DEFAULT_BUTTON_CLASS = 'w-full sm:w-auto';

    public bool $showModal = false;

    public string $buttonLabel = self::DEFAULT_BUTTON_LABEL;

    public string $buttonVariant = self::DEFAULT_BUTTON_VARIANT;

    public string $buttonClass = self::DEFAULT_BUTTON_CLASS;

    public ?int $timeEntryId = null;

    public ?string $errorMessage = null;

    // -------------------------------------------------------------------------
    // Lifecycle
    // -------------------------------------------------------------------------

    public function mount(
        string $buttonLabel = self::DEFAULT_BUTTON_LABEL,
        string $buttonVariant = self::DEFAULT_BUTTON_VARIANT,
        string $buttonClass = self::DEFAULT_BUTTON_CLASS,
    ): void {
        $this->buttonLabel = $buttonLabel;
        $this->buttonVariant = $buttonVariant;
        $this->buttonClass = $buttonClass;
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.time-entries.delete', [
            'canDelete' => $this->canDelete(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Modal actions
    // -------------------------------------------------------------------------

    #[On('open-delete-time-entry-modal')]
    public function openModal(int $id): void
    {
        $this->authorizeDelete();

        $this->timeEntryId = $id;
        $this->errorMessage = null;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->errorMessage = null;
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    public function delete(TimeEntryService $timeEntryService): void
    {
        $this->errorMessage = null;
        $this->authorizeDelete();

        $timeEntry = $this->resolveTimeEntry();

        try {
            $timeEntryService->delete($timeEntry, Auth::user());

            session()->flash('success', 'Entry deleted successfully.');

            $this->resetState();
            $this->closeModal();
            $this->dispatchDeletedEvent();
        } catch (\Exception $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    private function authorizeDelete(): void
    {
        if (! $this->canDelete()) {
            abort(403, 'You do not have permission for this action.');
        }
    }

    private function canDelete(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->hasPermission('time-entries.delete.own');
    }

    private function resolveTimeEntry(): TimeEntry
    {
        if ($this->timeEntryId === null) {
            abort(404, 'Time entry not found.');
        }

        $timeEntry = TimeEntry::query()
            ->visible()
            ->find($this->timeEntryId);

        if ($timeEntry === null) {
            abort(404, 'Time entry not found.');
        }

        return $timeEntry;
    }

    private function resetState(): void
    {
        $this->reset(['timeEntryId', 'errorMessage']);
    }

    private function dispatchDeletedEvent(): void
    {
        $this->dispatch('time-entry-deleted');
    }
}
