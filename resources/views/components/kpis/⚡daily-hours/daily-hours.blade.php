<flux:card class="h-full space-y-1">
    <flux:heading class="flex items-center gap-2">
        Horas hoje
        <flux:icon name="clock" class="ml-auto h-5 w-5 text-zinc-400" />
    </flux:heading>

    <div class="text-3xl font-semibold tracking-tight text-zinc-900 dark:text-zinc-100">
        {{ $this->todayHours }}
        <span class="text-base font-medium text-zinc-500 dark:text-zinc-400">h</span>
    </div>

    <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
        @if ($this->todayEntriesCount > 0)
            {{ $this->todayEntriesCount }} {{ $this->todayEntriesCount === 1 ? 'registro' : 'registros' }} hoje
        @else
            Nenhum registro hoje
        @endif
    </flux:text>
</flux:card>
