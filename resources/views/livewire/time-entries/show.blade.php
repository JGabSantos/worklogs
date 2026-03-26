<flux:modal wire:model="showModal" class="w-full max-w-4xl">
    <div class="space-y-6">
        <div class="space-y-1">
            <flux:heading size="lg">{{ __('Entry details') }}</flux:heading>
            <flux:subheading>
                {{ __('View the details of this time entry.') }}
            </flux:subheading>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <flux:input name="date" label="{{ __('Date') }}" wire:model.blur="date" mask="99/99/9999"
                placeholder="dd/mm/aaaa" disabled />

            <flux:input name="location" label="Location" wire:model.blur="location" type="text"
                placeholder="{{ __('e.g.: Office, Client, Remote') }}" disabled />

            <flux:input name="start_time" label="{{ __('Start time') }}" wire:model.blur="start_time" type="text"
                mask="99:99" placeholder="hh:mm" disabled />

            <flux:input name="end_time" label="{{ __('End time') }}" wire:model.blur="end_time" type="text"
                mask="99:99" placeholder="hh:mm" disabled />

            <flux:input name="activity_type" label="{{ __('Activity type') }}" type="text"
                value="{{ __($activity_type) }}" disabled />

            <flux:input name="client" label="{{ __('Client') }}" type="text" value="{{ __($client) }}"
                disabled />
        </div>

        @if ($status === 'draft')
            <flux:badge color="yellow" class="ml-4">
                {{ __('Draft') }}
            </flux:badge>
        @elseif ($status === 'active')
            <flux:badge color="green" class="ml-4">
                {{ __('Active') }}
            </flux:badge>
        @endif

        <flux:textarea name="description" label="Description" wire:model.blur="description" rows="5"
            placeholder="Add notes about the work completed" disabled />


        <div class="flex justify-end">
            <flux:button type="button" wire:click="closeModal" variant="ghost">
                {{ __('Close') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
