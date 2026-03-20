<flux:card class="h-full space-y-1">
    <flux:heading class="flex items-center gap-2">
        {{ __('Monthly\'s hours') }}
        <flux:icon name="clock" class="ml-auto h-5 w-5 text-zinc-400" />
    </flux:heading>

    <div class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
        {{ $this->monthlyHours }}
        <span class="text-base font-medium text-zinc-500 dark:text-zinc-400">h</span>
    </div>

    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
        @if ($this->monthlyEntriesCount > 0)
            {{ __(':entries entries this month', ['entries' => $this->monthlyEntriesCount]) }}
        @else
            {{ __('No entries this month') }}
        @endif
    </flux:text>
</flux:card>
