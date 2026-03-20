<div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
    <div class="space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="space-y-1">
                <flux:heading size="xl">Entry details</flux:heading>
                <flux:subheading>
                    Review all details of this time entry.
                </flux:subheading>
            </div>

            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center sm:gap-3">
                <flux:button class="w-full sm:w-auto"
                    wire:click="$dispatch('open-edit-time-entry-modal', { id: {{ $timeEntryId }} })" variant="primary">
                    Edit
                </flux:button>
                <flux:button class="w-full sm:w-auto" :href="route('time-entries.index')" variant="ghost">
                    Back
                </flux:button>
            </div>
        </div>

        <flux:separator />

        <flux:card>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text size="sm" class="text-zinc-500">Date</flux:text>
                    <flux:heading size="sm" class="mt-1">{{ optional($timeEntry->date)->format('d/m/Y') }}
                    </flux:heading>
                </div>

                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text size="sm" class="text-zinc-500">Duration</flux:text>
                    <flux:heading size="sm" class="mt-1">{{ $timeEntry->duration_minutes }} min</flux:heading>
                </div>

                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text size="sm" class="text-zinc-500">Start</flux:text>
                    <flux:heading size="sm" class="mt-1">
                        {{ \Illuminate\Support\Str::of($timeEntry->start_time)->substr(0, 5) }}</flux:heading>
                </div>

                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text size="sm" class="text-zinc-500">End</flux:text>
                    <flux:heading size="sm" class="mt-1">
                        {{ \Illuminate\Support\Str::of($timeEntry->end_time)->substr(0, 5) }}</flux:heading>
                </div>

                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text size="sm" class="text-zinc-500">Location</flux:text>
                    <flux:heading size="sm" class="mt-1">{{ $timeEntry->location ?: 'Not set' }}
                    </flux:heading>
                </div>

                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text size="sm" class="text-zinc-500">Status</flux:text>
                    <flux:heading size="sm" class="mt-1">{{ $timeEntry->status }}</flux:heading>
                </div>

                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text size="sm" class="text-zinc-500">Activity type</flux:text>
                    <flux:heading size="sm" class="mt-1">{{ $timeEntry->activityType->name }}</flux:heading>
                </div>

                <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                    <flux:text size="sm" class="text-zinc-500">Client</flux:text>
                    <flux:heading size="sm" class="mt-1">{{ $timeEntry->client->name }}</flux:heading>
                </div>
            </div>

            <flux:separator class="my-6" />

            <div class="space-y-2">
                <flux:text size="sm" class="text-zinc-500">Description</flux:text>
                <flux:text>
                    {{ $timeEntry->description ?: 'No description.' }}
                </flux:text>
            </div>
        </flux:card>
    </div>
</div>
