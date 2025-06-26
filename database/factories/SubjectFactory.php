<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition()
    {
        return [
            'subject_code' => $this->faker->unique()->bothify('SBJ-###'),
            'subject_name' => $this->faker->randomElement(['Matematika', 'Bahasa Indonesia', 'IPA']),
            'category' => $this->faker->randomElement(['Wajib', 'Pilihan']),
            'grade_level' => $this->faker->randomElement(['10', '11', '12']),
            'major_id' => Major::query()->inRandomOrder()->value('id') ?? Major::factory(),
            'description' => $this->faker->sentence()
        ];
    }
}
