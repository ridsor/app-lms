<?php

namespace Database\Seeders;

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
                        $schedule->meetings()->create([
                            'date' => $tanggal->format('Y-m-d'),
                            'meeting_method' => $schedule->meeting_method,
                            'type' => 'Learning',
                        ]);
                    }
                }
            }
        });
    }
}
