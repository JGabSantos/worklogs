<?php

use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('all time includes entries from previous years by activity type', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $development = ActivityType::query()->create([
        'name' => 'Development',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $support = ActivityType::query()->create([
        'name' => 'Support',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Client A',
        'is_active' => true,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->subYears(2)->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '10:00:00',
        'duration_minutes' => 120,
        'location' => 'Remote',
        'activity_type_id' => $development->id,
        'client_id' => $client->id,
        'description' => 'Legacy dev work',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->toDateString(),
        'start_time' => '11:00:00',
        'end_time' => '12:30:00',
        'duration_minutes' => 90,
        'location' => 'Office',
        'activity_type_id' => $support->id,
        'client_id' => $client->id,
        'description' => 'Current support work',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    actingAs($user);

    Livewire::test('charts.hours-by-activity-type')
        ->set('period', 'all')
        ->assertSet('chart.series', function (array $series): bool {
            return (int) array_sum($series) === 210;
        });
});

test('thirty day period excludes old entries by activity type', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $oldActivity = ActivityType::query()->create([
        'name' => 'Old Activity',
        'sort_order' => 3,
        'is_active' => true,
    ]);

    $recentActivity = ActivityType::query()->create([
        'name' => 'Recent Activity',
        'sort_order' => 4,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Client B',
        'is_active' => true,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->subDays(120)->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '09:00:00',
        'duration_minutes' => 60,
        'location' => 'Remote',
        'activity_type_id' => $oldActivity->id,
        'client_id' => $client->id,
        'description' => 'Old activity',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->subDays(3)->toDateString(),
        'start_time' => '09:30:00',
        'end_time' => '10:30:00',
        'duration_minutes' => 60,
        'location' => 'Office',
        'activity_type_id' => $recentActivity->id,
        'client_id' => $client->id,
        'description' => 'Recent activity',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    actingAs($user);

    Livewire::test('charts.hours-by-activity-type')
        ->set('period', '30d')
        ->assertSet('chart.categories', function (array $categories) use ($oldActivity, $recentActivity): bool {
            return ! in_array($oldActivity->name, $categories, true)
                && in_array($recentActivity->name, $categories, true);
        });
});

test('hours by activity chart reloads after a new time entry is saved', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Analysis',
        'sort_order' => 5,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Client C',
        'is_active' => true,
    ]);

    actingAs($user);

    $component = Livewire::test('charts.hours-by-activity-type')
        ->set('period', 'all')
        ->assertSet('chart.categories', []);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->toDateString(),
        'start_time' => '13:00:00',
        'end_time' => '14:30:00',
        'duration_minutes' => 90,
        'location' => 'Office',
        'activity_type_id' => $activityType->id,
        'client_id' => $client->id,
        'description' => 'Fresh activity entry',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $component
        ->call('refreshChart')
        ->assertSet('chart.categories', [$activityType->name])
        ->assertSet('chart.series', [90]);
});

test('hours by activity view renders pie chart with percentage labels', function () {
    $viewPath = resource_path('views/components/charts/⚡hours-by-activity-type/hours-by-activity-type.blade.php');

    expect(file_get_contents($viewPath))
        ->toContain("type: 'donut'")
        ->toContain('MutationObserver')
        ->toContain('toFixed(1)}%');
});
