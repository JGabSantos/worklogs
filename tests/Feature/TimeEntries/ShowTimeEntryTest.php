<?php

use App\Livewire\TimeEntries\Show;
use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('show component loads the authenticated users visible entry', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Support',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Client Detail',
        'is_active' => true,
    ]);

    $timeEntry = createVisibleTimeEntry($user, $activityType, $client, [
        'description' => 'Detailed activity',
        'location' => 'Office',
    ]);

    actingAs($user);

    Livewire::test(Show::class, ['id' => $timeEntry->id])
        ->assertSet('timeEntryId', $timeEntry->id)
        ->assertSee('Client Detail')
        ->assertSee('Support')
        ->assertSee('Detailed activity')
        ->assertSee('Office');
});

test('show component returns not found for deleted entries', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Support',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Client Detail',
        'is_active' => true,
    ]);

    $timeEntry = createVisibleTimeEntry($user, $activityType, $client, [
        'status' => 'deleted',
    ]);

    actingAs($user);

    expect(fn () => Livewire::test(Show::class, ['id' => $timeEntry->id]))
        ->toThrow(ModelNotFoundException::class);
});

test('show component does not expose another users entry', function () {
    $owner = User::factory()->createOne();
    assert($owner instanceof User);

    $viewer = User::factory()->createOne();
    assert($viewer instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Analysis',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Private Client',
        'is_active' => true,
    ]);

    $timeEntry = createVisibleTimeEntry($owner, $activityType, $client);

    actingAs($viewer);

    expect(fn () => Livewire::test(Show::class, ['id' => $timeEntry->id]))
        ->toThrow(ModelNotFoundException::class);
});

function createVisibleTimeEntry(
    User $user,
    ActivityType $activityType,
    Client $client,
    array $attributes = [],
): TimeEntry {
    return TimeEntry::query()->create(array_merge([
        'user_id' => $user->id,
        'date' => '2026-03-20',
        'start_time' => '09:00:00',
        'end_time' => '10:30:00',
        'duration_minutes' => 90,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'Visible work',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ], $attributes));
}
