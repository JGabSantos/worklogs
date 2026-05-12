<?php

use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('weekly hours kpi uses the current week boundaries', function () {
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
        'date' => now()->startOfWeek()->addDay()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '11:00:00',
        'duration_minutes' => 120,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'This week entry',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->startOfWeek()->subDay()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
        'duration_minutes' => 180,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'Last week entry',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    actingAs($user);

    Livewire::test('kpis.weekly-hours')
        ->assertSee('2:00')
        ->assertSee('1 registro esta semana');
});
