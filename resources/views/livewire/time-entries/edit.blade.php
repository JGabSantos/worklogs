<flux:modal wire:model="showModal" class="w-full max-w-4xl">
    <div class="space-y-6">
        <div class="space-y-1">
            <flux:heading size="lg">{{ __('Edit entry') }}</flux:heading>
            <flux:subheading>
                {{ __('Update the details of your time entry. Make sure to save changes before closing') }}
            </flux:subheading>
        </div>

        <form wire:submit="save" class="space-y-6">
            @if ($errorMessage)
                <flux:callout variant="danger" icon="exclamation-triangle">
                    {{ __($errorMessage) }}
                </flux:callout>
            @endif

            <div class="grid gap-4 md:grid-cols-2">
                <flux:input name="date" label="{{ __('Date') }}" wire:model.blur="date" mask="99/99/9999"
                    placeholder="dd/mm/aaaa" required />

                <flux:input name="location" label="Location" wire:model.blur="location" type="text"
                    placeholder="{{ __('e.g.: Office, Client, Remote') }}" required />

                <flux:input name="start_time" label="{{ __('Start time') }}" wire:model.blur="start_time" type="text"
                    mask="99:99" placeholder="hh:mm" required />

                <flux:input name="end_time" label="{{ __('End time') }}" wire:model.blur="end_time" type="text"
                    mask="99:99" placeholder="hh:mm" required />

                <x-autocomplete fieldLabel="{{ __('Activity type') }}" placeholder="{{ __('Search activity type') }}"
                    alpineOpenVar="isActivityTypeOpen" wireModel="activityTypeSearch" :suggestions="$activityTypeSuggestions"
                    :selectedId="$activity_type_id" selectAction="selectActivityType" clearAction="clearActivityTypeSelection"
                    emptyMessage="{{ __('No activity types found.') }}" />

                <x-autocomplete fieldLabel="Client" placeholder="{{ __('Search client') }}"
                    alpineOpenVar="isClientOpen" wireModel="clientSearch" :suggestions="$clientSuggestions" :selectedId="$client_id"
                    selectAction="selectClient" clearAction="clearClientSelection"
                    emptyMessage="{{ __('No clients found.') }}" />

                <flux:select name="status" wire:model="status" label="Status" required>
                    <flux:select.option value="draft">
                        {{ __('Draft') }}
                    </flux:select.option>
                    <flux:select.option value="active">
                        {{ __('Active') }}
                    </flux:select.option>
                </flux:select>
            </div>

            <flux:textarea name="description" label="{{ __('Description') }}" wire:model.blur="description"
                rows="5" placeholder="{{ __('Add notes about the work completed') }}" required />

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <flux:button type="button" wire:click="closeModal" variant="ghost">
                    {{ __('Cancel') }}
                </flux:button>

                <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">
                    {{ __('Save') }}
                </flux:button>
            </div>
        </form>
    </div>
</flux:modal>
