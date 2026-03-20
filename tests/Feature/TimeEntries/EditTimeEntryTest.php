<?php

use App\Livewire\TimeEntries\Edit;
use App\Models\ActivityType;
use App\Models\Client;
use App\Models\Permission;
use App\Models\Profile;
use App\Models\TimeEntry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('edit component loads the selected entry for an authorized user', function () {
    $user = createUserWithPermissions(['time-entries.update.own']);

    $activityType = ActivityType::query()->create([
        'name' => 'Development',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Acme Corp',
        'is_active' => true,
    ]);

    $timeEntry = createTimeEntryForUser(
        user: $user,
        activityType: $activityType,
        client: $client,
        attributes: [
            'date' => '2026-03-18',
            'start_time' => '09:15:00',
            'end_time' => '10:45:00',
            'location' => 'Office',
            'description' => 'Sprint review',
            'status' => 'active',
            'duration_minutes' => 90,
        ],
    );

    actingAs($user);

    Livewire::test(Edit::class, ['id' => $timeEntry->id])
        ->assertSet('showModal', true)
        ->assertSet('timeEntryId', $timeEntry->id)
        ->assertSet('date', '18/03/2026')
        ->assertSet('start_time', '09:15')
        ->assertSet('end_time', '10:45')
        ->assertSet('activity_type_id', (string) $activityType->id)
        ->assertSet('client_id', (string) $client->id)
        ->assertSet('activityTypeSearch', $activityType->name)
        ->assertSet('clientSearch', $client->name)
        ->assertSet('description', 'Sprint review');
});

test('editing a time entry persists the changes and dispatches refresh event', function () {
    $user = createUserWithPermissions(['time-entries.update.own']);

    $originalActivityType = ActivityType::query()->create([
        'name' => 'Support',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $newActivityType = ActivityType::query()->create([
        'name' => 'Analysis',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $originalClient = Client::query()->create([
        'name' => 'Legacy Corp',
        'is_active' => true,
    ]);

    $newClient = Client::query()->create([
        'name' => 'Modern Inc',
        'is_active' => true,
    ]);

    $timeEntry = createTimeEntryForUser(
        user: $user,
        activityType: $originalActivityType,
        client: $originalClient,
        attributes: [
            'date' => '2026-03-18',
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'location' => 'Office',
            'description' => 'Old work',
            'status' => 'draft',
            'duration_minutes' => 60,
        ],
    );

    actingAs($user);

    Livewire::test(Edit::class, ['id' => $timeEntry->id])
        ->set('date', '19/03/2026')
        ->set('location', 'Remote')
        ->set('start_time', '10:00')
        ->set('end_time', '12:15')
        ->set('activity_type_id', (string) $newActivityType->id)
        ->set('client_id', (string) $newClient->id)
        ->set('description', 'Updated work log')
        ->set('status', 'active')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('time-entry-created')
        ->assertSet('showModal', false)
        ->assertSet('timeEntryId', null);

    $timeEntry->refresh();

    expect($timeEntry->date?->format('Y-m-d'))->toBe('2026-03-19');
    expect($timeEntry->start_time?->format('H:i:s'))->toBe('10:00:00');
    expect($timeEntry->end_time?->format('H:i:s'))->toBe('12:15:00');
    expect($timeEntry->duration_minutes)->toBe(135);
    expect($timeEntry->location)->toBe('Remote');
    expect($timeEntry->activity_type_id)->toBe($newActivityType->id);
    expect($timeEntry->client_id)->toBe($newClient->id);
    expect($timeEntry->description)->toBe('Updated work log');
    expect($timeEntry->status)->toBe('active');
});

test('edit component forbids users without update permission', function () {
    $owner = createUserWithPermissions(['time-entries.update.own']);

    $activityType = ActivityType::query()->create([
        'name' => 'Development',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Acme Corp',
        'is_active' => true,
    ]);

    $timeEntry = createTimeEntryForUser($owner, $activityType, $client);

    $unauthorizedUser = User::factory()->createOne([
        'is_active' => true,
    ]);
    assert($unauthorizedUser instanceof User);

    actingAs($unauthorizedUser);

    Livewire::test(Edit::class, ['id' => $timeEntry->id])
        ->assertForbidden();
});

function createUserWithPermissions(array $permissionCodes): User
{
    $profile = Profile::query()->create([
        'code' => fake()->unique()->slug(),
        'name' => fake()->unique()->jobTitle(),
        'is_active' => true,
    ]);

    foreach ($permissionCodes as $permissionCode) {
        $permission = Permission::query()->create([
            'code' => $permissionCode,
            'name' => str($permissionCode)->replace('.', ' ')->title()->toString(),
        ]);

        $profile->permissions()->attach($permission);
    }

    $user = User::factory()->createOne([
        'profile_id' => $profile->id,
        'is_active' => true,
    ]);
    assert($user instanceof User);

    return $user;
}

function createTimeEntryForUser(
    User $user,
    ActivityType $activityType,
    Client $client,
    array $attributes = [],
): TimeEntry {
    return TimeEntry::query()->create(array_merge([
        'user_id' => $user->id,
        'date' => '2026-03-20',
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'duration_minutes' => 60,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'Focused work',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ], $attributes));
}
