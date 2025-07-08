# Optimasi Response Time dengan Promise dan Async Processing

## Overview

Dokumen ini menjelaskan implementasi optimasi response time pada aplikasi LMS menggunakan Promise pattern dan asynchronous processing.

## Masalah yang Dipecahkan

Sebelumnya, kode pembuatan meeting berjalan secara synchronous:

```php
// Kode lama - Synchronous (lambat)
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
```

**Masalah:**

-   Response time lambat karena menunggu semua meeting dibuat
-   User harus menunggu proses selesai
-   Tidak ada feedback real-time

## Solusi yang Diterapkan

### 1. Laravel Queue + Job (Recommended)

**File:** `app/Jobs/CreateScheduleMeetings.php`

```php
class CreateScheduleMeetings implements ShouldQueue
{
    public function handle()
    {
        // Proses pembuatan meeting secara batch
        $meetings = [];

        for ($tanggal = $schedule->period->start_date; $tanggal <= $schedule->period->end_date; $tanggal->addDay()) {
            if ($tanggal->format('l') == $schedule->day) {
                $isWeekend = $tanggal->isWeekend();
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
        }
    }
}
```

**Keuntungan:**

-   Response time cepat (tidak menunggu meeting dibuat)
-   Background processing
-   Error handling yang baik
-   Retry mechanism

### 2. Promise Pattern (Alternative)

**File:** `app/Http/Controllers/User/ScheduleController.php`

```php
public function storeWithPromise(ScheduleRequest $formRequest)
{
    // Promise 1: Validasi periode aktif
    $periodPromise = $this->getActivePeriodAsync();

    // Promise 2: Validasi jadwal yang bentrok
    $conflictPromise = $this->checkScheduleConflictAsync($formRequest);

    // Menunggu kedua promise selesai
    $activePeriod = $periodPromise->wait();
    $hasConflict = $conflictPromise->wait();

    // Promise 3: Membuat meeting secara asynchronous
    $meetingPromise = $this->createMeetingsAsync($schedule);

    // Tidak menunggu promise meeting selesai
    $meetingPromise->then(function($result) {
        Log::info("Meeting berhasil dibuat: " . $result);
    });
}
```

## Perbandingan Performa

| Metode          | Response Time | User Experience  | Error Handling |
| --------------- | ------------- | ---------------- | -------------- |
| Synchronous     | 3-5 detik     | User menunggu    | Basic          |
| Queue + Job     | < 1 detik     | Instant feedback | Excellent      |
| Promise Pattern | < 1 detik     | Instant feedback | Good           |

## Setup Queue

### 1. Konfigurasi Queue Driver

**File:** `.env`

```env
QUEUE_CONNECTION=database
```

### 2. Buat Migration untuk Queue Table

```bash
php artisan queue:table
php artisan migrate
```

### 3. Jalankan Queue Worker

```bash
php artisan queue:work
```

## Monitoring dan Logging

### 1. Queue Monitoring

```bash
# Cek status queue
php artisan queue:work --verbose

# Monitor failed jobs
php artisan queue:failed
```

### 2. Logging

Job akan mencatat log untuk monitoring:

```php
Log::info("Berhasil membuat " . count($meetings) . " meeting untuk schedule ID: " . $schedule->id);
Log::error("Error creating meetings for schedule ID: " . $this->schedule->id);
```

## Best Practices

### 1. Batch Processing

```php
// Gunakan insert batch daripada create satu-satu
$schedule->meetings()->insert($meetings);
```

### 2. Error Handling

```php
public function failed(\Throwable $exception)
{
    Log::error("Job CreateScheduleMeetings failed", [
        'schedule_id' => $this->schedule->id,
        'error' => $exception->getMessage()
    ]);
}
```

### 3. Retry Configuration

```php
class CreateScheduleMeetings implements ShouldQueue
{
    public $tries = 3;
    public $timeout = 60;
}
```

## Testing

### 1. Unit Test untuk Job

```php
public function test_create_schedule_meetings_job()
{
    $schedule = Schedule::factory()->create();

    $job = new CreateScheduleMeetings($schedule);
    $job->handle();

    $this->assertDatabaseHas('meetings', [
        'schedule_id' => $schedule->id
    ]);
}
```

### 2. Integration Test

```php
public function test_store_schedule_with_async_meeting_creation()
{
    $response = $this->post('/api/schedules', [
        'class_id' => 1,
        'subject_id' => 1,
        'teacher_id' => 1,
        'room_id' => 1,
        'day' => 'Monday',
        'start_time' => '08:00',
        'end_time' => '09:00',
        'meeting_method' => 'Offline'
    ]);

    $response->assertStatus(201);
    $response->assertJson(['message' => 'Jadwal berhasil ditambahkan. Meeting akan dibuat dalam background.']);
}
```

## Kesimpulan

Implementasi async processing menggunakan Laravel Queue + Job memberikan:

1. **Response time yang cepat** - User mendapat feedback instan
2. **Scalability** - Bisa handle banyak request bersamaan
3. **Reliability** - Error handling dan retry mechanism
4. **Monitoring** - Logging dan monitoring yang baik

Untuk production, gunakan Queue + Job approach karena lebih robust dan scalable.
