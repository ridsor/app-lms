<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CreateScheduleMeetings implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $schedule;

  /**
   * Create a new job instance.
   *
   * @param Schedule $schedule
   * @return void
   */
  public function __construct(Schedule $schedule)
  {
    $this->schedule = $schedule;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    try {
      // Load schedule dengan relasi period
      $schedule = $this->schedule->load('period');

      // Membuat meeting secara batch untuk performa yang lebih baik
      $meetings = [];

      for ($tanggal = $schedule->period->start_date; $tanggal <= $schedule->period->end_date; $tanggal->addDay()) {
        if ($tanggal->format('l') == $schedule->day) {
          $isWeekend = $tanggal->isWeekend(); // true jika Sabtu/Minggu

          if (!$isWeekend) {
            $meetings[] = [
              'schedule_id' => $schedule->id,
              'date' => $tanggal->format('Y-m-d'),
              'meeting_method' => $schedule->meeting_method,
              'type' => 'Learning',
              'created_at' => now(),
              'updated_at' => now(),
            ];
          }
        }
      }

      // Insert batch untuk performa yang lebih baik
      if (!empty($meetings)) {
        $schedule->meetings()->insert($meetings);
        Log::info("Berhasil membuat " . count($meetings) . " meeting untuk schedule ID: " . $schedule->id);
      }
    } catch (\Exception $e) {
      Log::error("Error creating meetings for schedule ID: " . $this->schedule->id, [
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
    Log::error("Job CreateScheduleMeetings failed for schedule ID: " . $this->schedule->id, [
      'error' => $exception->getMessage(),
      'trace' => $exception->getTraceAsString()
    ]);
  }
}
