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
            'class_id' => SchoolClass::first()?->id ?? SchoolClass::factory()->create()->id,
            'subject_id' => Subject::inRandomOrder()->first()?->id ?? Subject::factory()->create()->id,
            'teacher_id' => Teacher::inRandomOrder()->first()?->id ?? Teacher::factory()->create()->id,
            'room_id' => Room::inRandomOrder()->first()?->id ?? Room::factory()->create()->id,
            'period_id' => Period::first()?->id ?? Period::factory()->create()->id,
        ];
    }
}
