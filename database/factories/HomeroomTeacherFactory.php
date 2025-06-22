<?php

namespace Database\Factories;

use App\Models\HomeroomTeacher;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class HomeroomTeacherFactory extends Factory
{
    protected $model = HomeroomTeacher::class;

    public function definition()
    {
        return [
            'teacher_id' => Teacher::factory(),
        ];
    }
}
