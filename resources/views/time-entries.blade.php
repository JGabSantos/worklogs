<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="space-y-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-1">
                    <flux:heading size="xl">
                        {{ __('My entries') }}
                    </flux:heading>
                    <flux:subheading>
                        {{ __('Review, filter, and manage your time entries') }}
                    </flux:subheading>
                </div>

                <div>
                    <flux:modal.trigger name="time-entries.create" variant="primary">
                        <flux:button variant="primary" icon="plus">
                            {{ __('New entry') }}
                        </flux:button>
                    </flux:modal.trigger>

                    <livewire:time-entries.create />
                </div>
            </div>

            <flux:separator />

            <livewire:time-entries.index />
        </div>
    </div>

    <livewire:time-entries.edit />
    <livewire:time-entries.show />
    <livewire:time-entries.delete />
</x-layouts::app>
