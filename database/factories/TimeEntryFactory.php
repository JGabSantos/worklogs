<?php

namespace Database\Factories;

use App\Models\ActivityType;
use App\Models\Client;
use App\Models\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = \Carbon\Carbon::createFromTime(rand(8, 15), 0);
        $end = (clone $start)->addHours(rand(1, 3));

        return [
            'user_id' => User::inRandomOrder()->first()?->id,
            'date' => $this->faker->date(),
            'start_time' => $start->format('H:i:s'),
            'end_time' => $end->format('H:i:s'),
            'duration_minutes' => $start->diffInMinutes($end),
            'location' => $this->faker->randomElement(['Escritório', 'Remoto']),
            'activity_type_id' => ActivityType::inRandomOrder()->first()?->id,
            'client_id' => Client::inRandomOrder()->first()?->id,
            'description' => $this->faker->sentence(),
            'status' => 'active',
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }
}
