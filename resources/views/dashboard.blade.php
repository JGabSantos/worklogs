<x-layouts::app title="Dashboard">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="space-y-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <flux:heading size="xl">
                        Dashboard
                    </flux:heading>
                    <flux:subheading>
                        Acompanhe seus registros de horas e visualize insights sobre sua produtividade.
                    </flux:subheading>
                </div>

                <div>
                    <livewire:time-entries.create />
                </div>
            </div>

            <flux:separator />

            {{-- KPIs   --}}
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3">
                <livewire:kpis.daily-hours />
                <livewire:kpis.weekly-hours />
                <livewire:kpis.monthly-hours />
            </div>

            {{-- Charts --}}
            <div class="grid min-w-0 gap-4 md:grid-cols-2">
                <livewire:charts.hours-by-client />
                <livewire:charts.hours-by-activity-type />
            </div>

            {{-- Recent entries --}}
            <div class="space-y-4">
                <livewire:time-entries.recents />
            </div>
        </div>
    </div>
</x-layouts::app>
