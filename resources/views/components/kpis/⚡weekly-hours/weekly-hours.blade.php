<flux:card class="h-full space-y-1">
    <flux:heading class="flex items-center gap-2">
        {{ __('Weekly\'s hours') }}
        <flux:icon name="clock" class="ml-auto h-5 w-5 text-zinc-400" />
    </flux:heading>

    <div class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
        {{ $this->weeklyHours }}
        <span class="text-base font-medium text-zinc-500 dark:text-zinc-400">h</span>
    </div>

    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
        @if ($this->weeklyEntriesCount > 0)
            {{ __(':entries entries this week', ['entries' => $this->weeklyEntriesCount]) }}
        @else
            {{ __('No entries this week') }}
        @endif
    </flux:text>
</flux:card>
