<?php

namespace App\Livewire\TimeEntries;

use App\Models\ActivityType;
use App\Models\Client;
use App\Services\TimeEntryService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class Create extends Component
{
    private const DEFAULT_STATUS = 'draft';

    private const DEFAULT_BUTTON_LABEL = 'Novo registro';

    private const DEFAULT_BUTTON_VARIANT = 'primary';

    private const DEFAULT_BUTTON_CLASS = 'w-full sm:w-auto';

    public bool $showModal = false;

    public string $buttonLabel = self::DEFAULT_BUTTON_LABEL;

    public string $buttonVariant = self::DEFAULT_BUTTON_VARIANT;

    public string $buttonClass = self::DEFAULT_BUTTON_CLASS;

    public string $date = '';

    public string $location = '';

    public string $start_time = '';

    public string $end_time = '';

    public string $description = '';

    public string $status = self::DEFAULT_STATUS;

    public string $activity_type_id = '';

    public string $client_id = '';

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
        $this->authorizeCreate();
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render(): View
    {
        return view('livewire.time-entries.create', [
            'canCreate' => $this->canCreate(),
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

    public function toggleModal(): void
    {
        if ($this->showModal) {
            $this->closeModal();
        } else {
            $this->openModal();
        }
    }

    #[On('open-create-time-entry-modal')]
    public function openModal(): void
    {
        $this->authorizeCreate();

        $this->showModal = true;
        $this->errorMessage = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->errorMessage = null;
    }

    // -------------------------------------------------------------------------
    // Save
    // -------------------------------------------------------------------------

    public function save(TimeEntryService $timeEntryService): void
    {
        $this->errorMessage = null;
        $this->authorizeCreate();

        $validated = $this->validate();

        try {
            $timeEntryService->create($this->preparePayload($validated), Auth::user());

            session()->flash('success', 'Registro criado com sucesso.');

            $this->resetFormState();
            $this->closeModal();
            $this->dispatch('time-entry-created');
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

    private function authorizeCreate(): void
    {
        if (! $this->canCreate()) {
            abort(403, 'You do not have permission for this action.');
        }
    }

    private function canCreate(): bool
    {
        $user = Auth::user();

        return $user !== null && $user->hasPermission('time-entries.create.own');
    }

    private function preparePayload(array $validated): array
    {
        return [
            ...$validated,
            'date' => Carbon::createFromFormat('d/m/Y', $validated['date'])->toDateString(),
            'start_time' => $validated['start_time'].':00',
            'end_time' => $validated['end_time'].':00',
        ];
    }

    private function resetFormState(): void
    {
        $this->date = '';
        $this->location = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->activity_type_id = '';
        $this->client_id = '';
        $this->status = self::DEFAULT_STATUS;
        $this->description = '';
        $this->errorMessage = null;
    }
}
