<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->name(),
            'nis' => $this->faker->unique()->numerify('2020####'),
            'nisn' => $this->faker->unique()->numerify('0011######'),
            'class_id' => SchoolClass::factory(),
            'homeroom_teacher_id' => Teacher::factory(),
            'date_of_birth' => $this->faker->date(),
            'birthplace' => $this->faker->city(),
            'gender' => $this->faker->randomElement(['M', 'F']),
            'religion' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Budha']),
            'admission_year' => $this->faker->year(),
            'status' => $this->faker->randomElement(['active', 'transferred', 'graduated', 'dropout']),
        ];
    }
}
