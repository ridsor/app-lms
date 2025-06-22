<?php

namespace Database\Factories;

use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Room;
use App\Models\Period;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    public function definition()
    {
        return [
            'class_id' => SchoolClass::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => Teacher::factory(),
            'room_id' => Room::factory(),
            'period_id' => Period::factory(),
            'day' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']),
            'meeting_method' => $this->faker->randomElement(['online', 'offline', 'hybrid']),
            'start_time' => $this->faker->time('H:i'),
            'end_time' => $this->faker->time('H:i')
        ];
    }
}
