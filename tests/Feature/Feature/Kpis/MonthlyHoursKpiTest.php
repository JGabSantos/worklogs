<?php

use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('monthly hours kpi excludes entries from previous months', function () {
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
        'date' => now()->startOfMonth()->addDays(2)->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '11:30:00',
        'duration_minutes' => 150,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'This month entry',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->subMonth()->endOfMonth()->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '13:00:00',
        'duration_minutes' => 240,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'Last month entry',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    actingAs($user);

    Livewire::test('kpis.monthly-hours')
        ->assertSee('2:30')
        ->assertSee('1 entries this month');
});
