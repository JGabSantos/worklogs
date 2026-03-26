<flux:card class="space-y-4">
    <div class="flex items-center justify-between gap-3">
        <div class="space-y-1">
            <flux:heading size="lg">
                {{ __('Recent entries') }}
            </flux:heading>
            <flux:subheading>
                {{ __('Your most recent time entries') }}
            </flux:subheading>
        </div>

        <flux:button variant="ghost" size="sm" :href="route('time-entries.index')">
            {{ __('View all') }}
        </flux:button>
    </div>

    @if ($latestEntries->isEmpty())
        <div class="rounded-lg border border-dashed border-zinc-200 px-6 py-8 text-center dark:border-zinc-700">
            <flux:text class="text-zinc-500">
                {{ __('No recent entries found') }}
            </flux:text>
        </div>
    @else
        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Date') }}</flux:table.column>
                <flux:table.column>{{ __('Duration') }}</flux:table.column>
                <flux:table.column>{{ __('Type') }}</flux:table.column>
                <flux:table.column>{{ __('Client') }}</flux:table.column>
                <flux:table.column>{{ __('Status') }}</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach ($latestEntries as $entry)
                    <flux:table.row :key="$entry->id" wire:key="recent-entry-{{ $entry->id }}">
                        <flux:table.cell>{{ $entry->date->format('d/m/Y') }}
                            {{ $entry->start_time->format('H:i') }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $entry->duration_minutes }} min</flux:table.cell>
                        <flux:table.cell>{{ __($entry->activityType->name) }}</flux:table.cell>
                        <flux:table.cell>{{ $entry->client->name }}</flux:table.cell>
                        <flux:table.cell>
                            <x-status-badge :status="$entry->status" />
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>
    @endif
</flux:card>

</div>
