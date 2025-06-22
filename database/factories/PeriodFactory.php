<?php

namespace Database\Factories;

use App\Models\Period;
use Illuminate\Database\Eloquent\Factories\Factory;

class PeriodFactory extends Factory
{
    protected $model = Period::class;

    public function definition()
    {
        return [
            'semester' => $this->faker->randomElement(['odd', 'even']),
            'academic_year' => $this->faker->year() . '/' . ($this->faker->year() + 1),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'status' => $this->faker->boolean()
        ];
    }
}
