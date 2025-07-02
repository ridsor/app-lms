<?php

namespace Database\Factories;

use App\Models\Curriculum;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition()
    {
        return [
            'curriculum_id' => Curriculum::query()->inRandomOrder()->value('id'),
            'name' => $this->faker->unique()->word,
        ];
    }
}
