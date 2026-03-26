<flux:modal wire:model="showModal" class="w-full max-w-md">
    <div class="space-y-6">
        <div class="space-y-1">
            <flux:heading size="lg">{{ __('Delete entry') }}</flux:heading>
            <flux:subheading>
                {{ __('Are you sure you want to delete this time entry? This action cannot be undone') }}
            </flux:subheading>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button type="button" wire:click="closeModal" variant="ghost">
                {{ __('Cancel') }}
            </flux:button>

            <flux:button type="button" wire:click="delete" variant="danger" wire:loading.attr="disabled"
                wire:target="delete">
                {{ __('Delete') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
