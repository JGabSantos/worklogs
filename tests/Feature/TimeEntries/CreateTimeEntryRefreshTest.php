<?php

use App\Livewire\TimeEntries\Create;
use App\Models\ActivityType;
use App\Models\Client;
use App\Models\Permission;
use App\Models\Profile;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('creating a time entry dispatches the dashboard refresh event', function () {
    $permission = Permission::query()->create([
        'code' => 'time-entries.create.own',
        'name' => 'Create own time entries',
    ]);

    $profile = Profile::query()->create([
        'code' => 'manager',
        'name' => 'Manager',
        'is_active' => true,
    ]);

    $profile->permissions()->attach($permission);

    $user = User::factory()->createOne([
        'profile_id' => $profile->id,
        'is_active' => true,
    ]);
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

    actingAs($user);

    Livewire::test(Create::class)
        ->set('date', now()->format('d/m/Y'))
        ->set('location', 'Office')
        ->set('start_time', '09:00')
        ->set('end_time', '10:30')
        ->set('activity_type_id', (string) $activityType->id)
        ->set('client_id', (string) $client->id)
        ->set('description', 'Planning session')
        ->set('status', 'active')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('time-entry-created');
});
