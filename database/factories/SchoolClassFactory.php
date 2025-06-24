<?php

namespace Database\Factories;

use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition()
    {
        return [
            'name' => strtoupper($this->faker->unique()->bothify('?')),
            'level' => $this->faker->randomElement(['10', '11', '12']),
            'major_id' => \App\Models\Major::inRandomOrder()->first()?->id ?? 1,
            'capacity' => $this->faker->numberBetween(20, 40)
        ];
    }
}
