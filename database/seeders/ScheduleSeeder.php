<?php

namespace Database\Seeders;

use App\Models\Meeting;
use Illuminate\Database\Seeder;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        Schedule::factory()->count(10)->create()->each(function ($schedule) {
            for ($tanggal = $schedule->period->start_date; $tanggal <= $schedule->period->end_date; $tanggal->addDay()) {
                if ($tanggal->format('l') == $schedule->day) {
                    $isWeekend = $tanggal->isWeekend();

                    if (!$isWeekend) {
                        $meetings = Meeting::create([
                            'schedule_id' => $schedule->id,
                            'grouping_schedule_id' => $schedule->grouping_schedule,
                            'date' => $tanggal->format('Y-m-d'),
                            'meeting_method' => $schedule->meeting_method,
                            'type' => 'Learning',
                        ]);

                        // Ambil semua siswa di kelas
                        $students = $schedule->class->students;

                        // Buat data kehadiran untuk setiap siswa
                        $attendances = [];
                        foreach ($students as $student) {
                            $attendances[] = [
                                'user_id' => $student->user_id,
                                'status' => 'H',
                                'edit_by' => $student->user_id,
                            ];
                        }

                        $meetings->attendances()->createMany($attendances);
                    }
                }
            }
        });
    }
}
