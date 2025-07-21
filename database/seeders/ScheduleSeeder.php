<?php

namespace Database\Seeders;

use App\Models\Meeting;
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use Illuminate\Support\Facades\Log;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        Schedule::factory()->count(10)->create()->each(function ($schedule) {
            $schedule_time = ScheduleTime::factory()->create([
                'schedule_id' => $schedule->id,
            ]);

            $meetings = [];
            $maxMeeting = 16;
            for ($tanggal = $schedule->period->start_date; $tanggal <= $schedule->period->end_date; $tanggal->addDay()) {
                if (count($meetings) >= $maxMeeting) break;

                if ($tanggal->format('l') == $schedule_time->day) {
                    $isWeekend = $tanggal->isWeekend();

                    if (!$isWeekend) {
                        $meetings[] = [
                            'schedule_id' => $schedule->id,
                            'schedule_time_id' => $schedule_time->id,
                            'meeting_method' => $schedule_time->meeting_method,
                            'type' => 'Learning',
                        ];
                    }
                }
            }

            $meetings = $schedule->meetings()->createMany($meetings);
            $students = $schedule->class->students;
            foreach ($meetings as $meeting) {
                $attendances = [];
                foreach ($students as $student) {
                    $attendances[] = [
                        'user_id' => $student->user_id,
                        'status' => 'H',
                        'edit_by' => $student->user_id,
                    ];
                }

                $meeting->attendances()->createMany($attendances);
            }
        });
    }
}
