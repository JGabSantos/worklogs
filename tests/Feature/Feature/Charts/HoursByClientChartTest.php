<?php

use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('all time includes entries from previous years', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Development',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $legacyClient = Client::query()->create([
        'name' => 'Legacy Corp',
        'is_active' => true,
    ]);

    $currentClient = Client::query()->create([
        'name' => 'Current Inc',
        'is_active' => true,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->subYears(2)->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '10:00:00',
        'duration_minutes' => 120,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $legacyClient->id,
        'description' => 'Legacy migration',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:30:00',
        'duration_minutes' => 90,
        'location' => 'Office',
        'activity_type_id' => $activityType->id,
        'client_id' => $currentClient->id,
        'description' => 'Current sprint',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    actingAs($user);

    Livewire::test('charts.hours-by-client')
        ->set('period', 'all')
        ->assertSet('chart.series', function (array $series): bool {
            return (int) array_sum($series) === 210;
        });
});

test('thirty day period excludes old entries', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Support',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $oldClient = Client::query()->create([
        'name' => 'Old Client',
        'is_active' => true,
    ]);

    $recentClient = Client::query()->create([
        'name' => 'Recent Client',
        'is_active' => true,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->subDays(120)->toDateString(),
        'start_time' => '08:00:00',
        'end_time' => '09:00:00',
        'duration_minutes' => 60,
        'location' => 'Remote',
        'activity_type_id' => $activityType->id,
        'client_id' => $oldClient->id,
        'description' => 'Outdated work',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    TimeEntry::query()->create([
        'user_id' => $user->id,
        'date' => now()->subDays(3)->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '11:00:00',
        'duration_minutes' => 60,
        'location' => 'Office',
        'activity_type_id' => $activityType->id,
        'client_id' => $recentClient->id,
        'description' => 'Recent work',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    actingAs($user);

    Livewire::test('charts.hours-by-client')
        ->set('period', '30d')
        ->assertSet('chart.categories', function (array $categories) use ($oldClient, $recentClient): bool {
            return ! in_array($oldClient->name, $categories, true)
                && in_array($recentClient->name, $categories, true);
        });
});

test('chart reloads after a new time entry is saved', function () {
    $user = User::factory()->createOne();
    assert($user instanceof User);

    $activityType = ActivityType::query()->create([
        'name' => 'Analysis',
        'sort_order' => 3,
        'is_active' => true,
    ]);

    $client = Client::query()->create([
        'name' => 'Event Client',
        'is_active' => true,
    ]);

    actingAs($user);

    $component = Livewire::test('charts.hours-by-client')
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
        'description' => 'Fresh entry',
        'status' => 'active',
        'created_by' => $user->id,
        'updated_by' => $user->id,
    ]);

    $component
        ->call('refreshChart')
        ->assertSet('chart.categories', [$client->name])
        ->assertSet('chart.series', [90]);
});

test('chart view includes dark and light mode handling', function () {
    $viewPath = resource_path('views/components/charts/⚡hours-by-client/hours-by-client.blade.php');

    expect(file_get_contents($viewPath))
        ->toContain('const CHART_THEME = {')
        ->toContain('MutationObserver')
        ->toContain("document.documentElement.classList.contains('dark')");
});
