<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScheduleTime>
 */
class ScheduleTimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'day' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
            'meeting_method' => $this->faker->randomElement(['online', 'offline', 'hybrid']),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i')
        ];
    }
}
