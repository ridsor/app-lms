<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Schedule;
use App\Models\Material;
use App\Models\Exam;
use App\Models\DiscussionForum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Mews\Purifier\Casts\CleanHtml;
use Illuminate\Support\Facades\Log;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'schedule_time_id',
        'title',
        'description',
        'meeting_method',
        'date',
        'holiday',
        'type',
        'started_at'
    ];

    protected $casts = [
        'date' => 'date',
        'started_at' => 'datetime',
        'description' => CleanHtml::class . ':strip_nl,strip_nbsp',
    ];

    protected $appends = ['status', 'formatted_date'];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function schedule_time(): BelongsTo
    {
        return $this->belongsTo(ScheduleTime::class, 'schedule_time_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function discussionForums(): HasMany
    {
        return $this->hasMany(DiscussionForum::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function teaching_journal(): HasOne
    {
        return $this->hasOne(TeachingJournal::class);
    }

    public function meeting_texts(): HasMany
    {
        return $this->hasMany(MeetingText::class);
    }

    public function getFormattedDateAttribute()
    {
        if ($this->date) {
            return $this->date->translatedFormat('j M Y');
        }
        return null;
    }

    public function getStatusAttribute()
    {
        // Jika belum ada waktu mulai, status belum dimulai
        if (!$this->started_at) {
            return 'Belum Dimulai';
        }

        $now = now();
        $start = $this->started_at;

        if (!$this->relationLoaded('schedule_time')) {
            $this->load('schedule_time');
        }

        if (!$this->schedule_time || empty($this->schedule_time->end_time)) {
            return 'Tidak Diketahui';
        }

        $endTime = $this->schedule_time->end_time;
        $end = $this->started_at->copy()->setTimeFromTimeString($endTime);

        if ($now->lt($start)) {
            return 'Belum Dimulai';
        }

        if ($now->gte($start) && $now->lte($end)) {
            return 'Sedang Berlangsung';
        }

        if ($now->gt($end)) {
            return 'Telah Berakhir';
        }

        return 'Tidak Diketahui';
    }
}
