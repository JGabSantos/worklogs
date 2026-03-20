<?php

use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('daily hours kpi shows only todays visible entries', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Development',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Client A',
        'is_active' => true,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '11:15:00',
        'duration_minutes' => 135,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'Today entry',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->subDay()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'duration_minutes' => 60,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'Yesterday entry',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->toDateString(),
        'start_time' => '13:00:00',
        'end_time' => '14:30:00',
        'duration_minutes' => 90,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'Deleted entry',
        'status' => 'deleted',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    actingAs($user);

    Livewire::test('kpis.daily-hours')
        ->assertSee('2:15')
        ->assertSee('1 entries today');
});
