<div class="space-y-6" x-data="{ isAdvancedOpen: @js($hasAdvancedFiltersActive) }">
    <div class="space-y-4">

        <div class="flex items-end gap-4">
            <div class="flex-1">
                <flux:input wire:model.live.debounce.300ms="search" label="{{ __('Search') }}"
                    placeholder="{{ __('ex.: Client, type or description') }}" clearable class="max-w-md" />
            </div>

            <button type="button" x-on:click="isAdvancedOpen = !isAdvancedOpen"
                x-bind:aria-expanded="isAdvancedOpen.toString()" aria-controls="advanced-filters"
                aria-label="{{ __('Toggle advanced filters') }}"
                class="relative inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border transition
                       border-zinc-200 text-zinc-600 hover:bg-zinc-100
                       dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800">
                <svg class="h-5 w-5 transition-transform duration-200" x-bind:class="isAdvancedOpen ? 'rotate-180' : ''"
                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>

                @if ($hasAdvancedFiltersActive)
                    <span
                        class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-zinc-900"></span>
                @endif
            </button>
        </div>

        <div id="advanced-filters" x-show="isAdvancedOpen" x-transition:enter="transition duration-200 ease-out"
            x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-1" x-cloak>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6">

                <flux:select wire:model.live="status" label="{{ __('Status') }}">
                    <flux:select.option value="">{{ __('All') }}</flux:select.option>
                    <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
                    <flux:select.option value="active">{{ __('Active') }}</flux:select.option>
                </flux:select>

                <x-autocomplete fieldLabel="{{ __('Client') }}" placeholder="{{ __('Search client') }}"
                    alpineOpenVar="isClientOpen" wireModel="clientSearch" :suggestions="$clientSuggestions" :selectedId="$client_id"
                    selectAction="selectClient" clearAction="clearClientSelection"
                    emptyMessage="{{ __('No clients found.') }}" />

                <x-autocomplete fieldLabel="{{ __('Activity type') }}" placeholder="{{ __('Search activity type') }}"
                    alpineOpenVar="isActivityTypeOpen" wireModel="activityTypeSearch" :suggestions="$activityTypeSuggestions"
                    :selectedId="$activity_type_id" selectAction="selectActivityType" clearAction="clearActivityTypeSelection"
                    emptyMessage="{{ __('No activity types found.') }}" />

                <flux:input wire:model.live="dateFromInput" label="{{ __('Start date') }}" placeholder="dd/mm/yyyy" />

                <flux:input wire:model.live="dateToInput" label="{{ __('End date') }}" placeholder="dd/mm/yyyy" />

                <div class="flex items-end">
                    <flux:button wire:click="clearFilters" variant="ghost" class="w-full">
                        {{ __('Clear filters') }}
                    </flux:button>
                </div>

            </div>
        </div>
    </div>

    <flux:card>

        <div wire:loading.delay class="mb-3">
            <flux:text class="text-zinc-500">{{ __('Loading...') }}</flux:text>
        </div>

        @if ($timeEntries->isEmpty())
            <div class="rounded-lg border border-dashed border-zinc-200 px-6 py-10 text-center dark:border-zinc-700">
                <flux:text class="text-zinc-500">
                    {{ __('Nothing found for the applied filters.') }}
                </flux:text>
            </div>
        @else
            <flux:table :paginate="$timeEntries">
                <flux:table.columns>
                    <flux:table.column sortable :sorted="$sortBy === 'date'" :direction="$orderBy"
                        wire:click="sort('date')">{{ __('Date') }}</flux:table.column>

                    <flux:table.column>{{ __('Start') }}</flux:table.column>
                    <flux:table.column>{{ __('End') }}</flux:table.column>

                    <flux:table.column sortable :sorted="$sortBy === 'duration_minutes'" :direction="$orderBy"
                        wire:click="sort('duration_minutes')">{{ __('Duration') }}</flux:table.column>

                    <flux:table.column>{{ __('Type') }}</flux:table.column>
                    <flux:table.column>{{ __('Client') }}</flux:table.column>
                    <flux:table.column>{{ __('Location') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach ($timeEntries as $entry)
                        <flux:table.row :key="$entry->id" wire:key="entry-{{ $entry->id }}">
                            <flux:table.cell>{{ $entry->date->format('d/m/Y') }}</flux:table.cell>
                            <flux:table.cell>{{ $entry->start_time->format('H:i') }}</flux:table.cell>
                            <flux:table.cell>{{ $entry->end_time->format('H:i') }}</flux:table.cell>
                            <flux:table.cell>{{ $entry->duration_minutes }} min</flux:table.cell>
                            <flux:table.cell>{{ __($entry->activityType->name) }}</flux:table.cell>
                            <flux:table.cell>{{ $entry->client->name }}</flux:table.cell>
                            <flux:table.cell>{{ $entry->location }}</flux:table.cell>

                            <flux:table.cell>
                                <x-status-badge :status="$entry->status" />
                            </flux:table.cell>

                            <flux:table.cell class="text-right">
                                <flux:dropdown>
                                    <flux:button icon:trailing="chevron-down">{{ __('Actions') }}</flux:button>

                                    <flux:menu>
                                        <flux:menu.item
                                            wire:click="$dispatch('open-show-time-entry-modal', { id: {{ $entry->id }} })">
                                            {{ __('Show') }}
                                        </flux:menu.item>
                                        <flux:menu.item
                                            wire:click="$dispatch('open-edit-time-entry-modal', { id: {{ $entry->id }} })">
                                            {{ __('Edit') }}
                                        </flux:menu.item>
                                        <flux:menu.separator />
                                        <flux:menu.item variant="danger"
                                            :href="route('time-entries.destroy', $entry->id)" method="delete">
                                            {{ __('Delete') }}
                                        </flux:menu.item>
                                    </flux:menu>
                                </flux:dropdown>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        @endif

    </flux:card>
</div>
