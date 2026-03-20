<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="space-y-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-1">
                    <flux:heading size="xl">
                        {{ __('My entries') }}
                    </flux:heading>
                    <flux:subheading>
                        {{ __('Review, filter, and manage your time entries.') }}
                    </flux:subheading>
                </div>

                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center sm:gap-3">
                    <livewire:time-entries.create />
                </div>
            </div>

            <flux:separator />

            <livewire:time-entries.index />
        </div>
    </div>

    <livewire:time-entries.edit />
</x-layouts::app>
