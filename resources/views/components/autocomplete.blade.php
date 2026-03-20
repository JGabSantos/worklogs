{{--
    Props:
        $fieldLabel     - string
        $placeholder    - string
        $alpineOpenVar  - string  (ex: 'isClientOpen')
        $wireModel      - string  (ex: 'clientSearch')
        $suggestions    - Collection
        $selectedId     - string
        $selectAction   - string  (ex: 'selectClient')
        $clearAction    - string  (ex: 'clearClientSelection')
        $emptyMessage   - string
--}}
<div class="relative" x-data="{ {{ $alpineOpenVar }}: false }" @click.outside="{{ $alpineOpenVar }} = false">
    <flux:field>
        <flux:label>{{ $fieldLabel }}</flux:label>

        <div class="relative">
            <flux:input wire:model.live.debounce.300ms="{{ $wireModel }}" placeholder="{{ $placeholder }}"
                x-on:focus="{{ $alpineOpenVar }} = true" x-on:keydown.escape.prevent="{{ $alpineOpenVar }} = false" />

            @if ($selectedId !== '')
                <button type="button" wire:click="{{ $clearAction }}"
                    class="absolute inset-y-0 right-2 flex items-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                    aria-label="{{ __('Clear selection') }}">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    </flux:field>

    <div x-show="{{ $alpineOpenVar }}" x-transition:enter="transition duration-150 ease-out"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-cloak
        class="absolute z-20 mt-2 w-full overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
        @if ($suggestions->isEmpty())
            <p class="px-3 py-2 text-sm text-zinc-500 dark:text-zinc-400">
                {{ $emptyMessage }}
            </p>
        @else
            <ul class="max-h-64 overflow-y-auto py-1" role="listbox">
                @foreach ($suggestions as $item)
                    <li role="option" :aria-selected="{{ $selectedId === (string) $item->id ? 'true' : 'false' }}">
                        <button type="button" wire:click="{{ $selectAction }}('{{ $item->id }}')"
                            x-on:click="{{ $alpineOpenVar }} = false"
                            class="flex w-full items-center justify-between px-3 py-2 text-left text-sm transition hover:bg-zinc-100 dark:hover:bg-zinc-800">
                            <span>{{ __($item->name) }}</span>

                            @if ($selectedId === (string) $item->id)
                                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                    {{ __('Selected') }}
                                </span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
