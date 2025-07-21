<?php

namespace App\Jobs;

use App\Models\Meeting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UpdateScheduleMeetings implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $schedule;
  protected $schedule_time;

  /**
   * Create a new job instance.
   *
   * @param Schedule $schedule
   * @param array $oldScheduleData
   * @return void
   */
  public function __construct(Schedule $schedule, ScheduleTime $schedule_time)
  {
    $this->schedule = $schedule;
    $this->schedule_time = $schedule_time;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    try {
      $schedule = $this->schedule->load('period');

      // Hapus semua meeting yang ada untuk jadwal ini
      $deletedCount = $schedule->meetings()->delete();
      Log::info("Menghapus {$deletedCount} meeting lama untuk schedule ID: " . $schedule->id);

      // Buat meeting baru berdasarkan jadwal yang diupdate
      $meetings = [];
      $maxMeeting = 16;
      for ($tanggal = $schedule->period->start_date; $tanggal <= $schedule->period->end_date; $tanggal->addDay()) {
        if (count($meetings) >= $maxMeeting) break;

        if ($tanggal->format('l') == $this->schedule_time->day) {
          $isWeekend = $tanggal->isWeekend();

          if (!$isWeekend) {
            $meetings[] = [
              'schedule_id' => $schedule->id,
              'schedule_time_id' => $this->schedule_time->id,
              'meeting_method' => $this->schedule_time->meeting_method,
              'type' => 'Learning',
            ];
          }
        }
      }

      // Insert batch untuk performa yang lebih baik
      if (!empty($meetings)) {
        // Perbaikan: createMany adalah method pada relasi, bukan pada model langsung
        $schedule->meetings()->createMany($meetings);
        Log::info("Berhasil membuat " . count($meetings) . " meeting baru untuk schedule ID: " . $schedule->id);
      } else {
        Log::info("Tidak ada meeting yang perlu dibuat untuk schedule ID: " . $schedule->id);
      }
    } catch (\Exception $e) {
      Log::error("Error updating meetings for schedule ID: " . $this->schedule->id, [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      // Re-throw exception agar job bisa di-retry
      throw $e;
    }
  }

  /**
   * Handle a job failure.
   *
   * @param \Throwable $exception
   * @return void
   */
  public function failed(\Throwable $exception)
  {
    Log::error("Job UpdateScheduleMeetings failed for schedule ID: " . $this->schedule->id, [
      'error' => $exception->getMessage(),
      'trace' => $exception->getTraceAsString()
    ]);
  }
}
