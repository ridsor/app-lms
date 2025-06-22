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
            'name' => $this->faker->unique()->bothify('Kelas ##'),
            'level' => $this->faker->randomElement(['10', '11', '12']),
            'major' => $this->faker->randomElement(['IPA', 'IPS', 'Bahasa']),
            'capacity' => $this->faker->numberBetween(20, 40)
        ];
    }
}
