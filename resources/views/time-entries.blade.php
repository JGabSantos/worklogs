<x-layouts::app title="Registros de horas">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="space-y-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="space-y-1">
                    <flux:heading size="xl">
                        Meus registros
                    </flux:heading>
                    <flux:subheading>
                        Visualize, filtre e gerencie seus registros de horas
                    </flux:subheading>
                </div>

                <div>
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
