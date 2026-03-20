<?php

use App\Livewire\TimeEntries\Recents;
use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('recents component shows only the five most recent visible entries', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Development',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Acme Corp',
        'is_active' => true,
    ]);

    createTimeEntryForRecentsTest(
        user: $user,
        activityTypeId: $activityType->id,
        clientId: $client->id,
        date: '2026-03-10',
        startTime: '08:00:00',
        description: 'Older entry',
    );

    $entry2 = createTimeEntryForRecentsTest(
        user: $user,
        activityTypeId: $activityType->id,
        clientId: $client->id,
        date: '2026-03-11',
        startTime: '08:00:00',
        description: 'Entry 2',
    );

    $entry3 = createTimeEntryForRecentsTest(
        user: $user,
        activityTypeId: $activityType->id,
        clientId: $client->id,
        date: '2026-03-12',
        startTime: '08:00:00',
        description: 'Entry 3',
    );

    $entry4 = createTimeEntryForRecentsTest(
        user: $user,
        activityTypeId: $activityType->id,
        clientId: $client->id,
        date: '2026-03-13',
        startTime: '08:00:00',
        description: 'Entry 4',
    );

    $entry5 = createTimeEntryForRecentsTest(
        user: $user,
        activityTypeId: $activityType->id,
        clientId: $client->id,
        date: '2026-03-14',
        startTime: '08:00:00',
        description: 'Entry 5',
    );

    $entry6 = createTimeEntryForRecentsTest(
        user: $user,
        activityTypeId: $activityType->id,
        clientId: $client->id,
        date: '2026-03-15',
        startTime: '08:00:00',
        description: 'Entry 6',
    );

    createTimeEntryForRecentsTest(
        user: $user,
        activityTypeId: $activityType->id,
        clientId: $client->id,
        date: '2026-03-16',
        startTime: '08:00:00',
        description: 'Deleted entry',
        status: 'deleted',
    );

    $expectedIds = [
        $entry6->id,
        $entry5->id,
        $entry4->id,
        $entry3->id,
        $entry2->id,
    ];

    actingAs($user);

    Livewire::test(Recents::class)
        ->assertViewHas('latestEntries', function ($latestEntries) use ($expectedIds) {
            return $latestEntries->pluck('id')->values()->all() === $expectedIds;
        })
        ->assertSee('15/03/2026')
        ->assertDontSee('10/03/2026');
});

function createTimeEntryForRecentsTest(
    User $user,
    int $activityTypeId,
    int $clientId,
    string $date,
    string $startTime,
    string $description,
    string $status = 'active',
): TimeEntry {
    $start = Carbon::parse($startTime);
    $end = (clone $start)->addHour();

    return TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => $date,
        'start_time' => $start->format('H:i:s'),
        'end_time' => $end->format('H:i:s'),
        'duration_minutes' => $start->diffInMinutes($end),
        'location' => 'Remote',
        'activity_type_id' => $activityTypeId,
        'client_id' => $clientId,
        'description' => $description,
        'status' => $status,
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);
}
