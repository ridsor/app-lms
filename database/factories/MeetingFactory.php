<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    public function definition()
    {
        return [
            'schedule_id' => Schedule::factory(),
            'meeting_number' => $this->faker->numberBetween(1, 16),
            'date' => $this->faker->date(),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'meeting_method' => $this->faker->randomElement(['online', 'offline', 'hybrid']),
            'type' => $this->faker->randomElement(['Learning', 'Midterm', 'Final']),
            'status' => $this->faker->randomElement(['not_started', 'started', 'ended']),
        ];
    }
}
