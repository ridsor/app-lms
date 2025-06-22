<?php

namespace Database\Factories;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'nip' => $this->faker->unique()->numerify('1980#######'),
            'specialization' => $this->faker->randomElement(['Matematika', 'Bahasa Indonesia', 'IPA']),
            'education_level' => $this->faker->randomElement(['S1', 'S2', 'S3']),
            'date_of_birth' => $this->faker->date(),
            'birthplace' => $this->faker->city(),
            'religion' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha'])
        ];
    }
}
