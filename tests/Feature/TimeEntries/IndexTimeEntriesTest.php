<?php

use App\Livewire\TimeEntries\Index;
use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('index component filters visible entries by search and status', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Development',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $matchingClient = Client::query()->create([
        'name' => 'Target Client',
        'is_active' => true,
    ]);

    $otherClient = Client::query()->create([
        'name' => 'Other Client',
        'is_active' => true,
    ]);

    createIndexedTimeEntry($user, $activityType, $matchingClient, [
        'description' => 'Main task',
        'status' => 'active',
        'date' => '2026-03-20',
    ]);

    createIndexedTimeEntry($user, $activityType, $otherClient, [
        'description' => 'Draft task',
        'status' => 'draft',
        'date' => '2026-03-19',
    ]);

    actingAs($user);

    Livewire::test(Index::class)
        ->set('search', 'Target')
        ->set('status', 'active')
        ->assertViewHas('timeEntries', function ($timeEntries) use ($matchingClient): bool {
            return $timeEntries->pluck('client.name')->all() === [$matchingClient->name];
        });
});

test('index component clears filters back to defaults', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    actingAs($user);

    Livewire::test(Index::class)
        ->set('search', 'Acme')
        ->set('dateFromInput', '01/03/2026')
        ->set('dateToInput', '15/03/2026')
        ->set('status', 'active')
        ->set('duration_min', '30')
        ->set('duration_max', '90')
        ->set('sort_by', 'duration_minutes')
        ->set('orderBy', 'asc')
        ->set('perPage', 25)
        ->call('clearFilters')
        ->assertSet('search', '')
        ->assertSet('dateFrom', '')
        ->assertSet('dateFromInput', '')
        ->assertSet('dateTo', '')
        ->assertSet('dateToInput', '')
        ->assertSet('status', '')
        ->assertSet('duration_min', '')
        ->assertSet('duration_max', '')
        ->assertSet('sort_by', 'date')
        ->assertSet('orderBy', 'desc')
        ->assertSet('perPage', 10);
});

test('refresh event resets the index pagination to the first page', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Development',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $pageOneClient = Client::query()->create([
        'name' => 'Page One Client',
        'is_active' => true,
    ]);

    $pageTwoClient = Client::query()->create([
        'name' => 'Page Two Client',
        'is_active' => true,
    ]);

    foreach (range(0, 9) as $offset) {
        createIndexedTimeEntry($user, $activityType, $pageOneClient, [
            'date' => now()->subDays($offset)->toDateString(),
            'description' => 'Page one entry '.$offset,
        ]);
    }

    createIndexedTimeEntry($user, $activityType, $pageTwoClient, [
        'date' => now()->subDays(20)->toDateString(),
        'description' => 'Page two entry',
    ]);

    actingAs($user);

    Livewire::test(Index::class)
        ->call('gotoPage', 2)
        ->assertSee('Showing 11 to 11 of 11 results')
        ->call('refreshEntries')
        ->assertSee('Showing 1 to 10 of 11 results');
});

function createIndexedTimeEntry(
    User $user,
    ActivityType $activityType,
    Client $client,
    array $attributes = [],
): TimeEntry {
    return TimeEntry::query()->create(array_merge([
        'user_id' => $user->id,
        'date' => now()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'duration_minutes' => 60,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'Indexed work',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ], $attributes));
}
