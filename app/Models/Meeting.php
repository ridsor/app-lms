<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Schedule;
use App\Models\Material;
use App\Models\Assignment;
use App\Models\Exam;
use App\Models\DiscussionForum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Meeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'grouping_schedule_id',
        'date',
        'title',
        'description',
        'meeting_method',
        'type',
        'started_at'
    ];

    protected $casts = [
        'date' => 'date',
        'started_at' => 'datetime',
    ];

    protected $appends = ['formatted_date', 'status', 'formatted_started_at'];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function grouping_schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'grouping_schedule_id', 'grouping_schedule');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
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

    public function getFormattedDateAttribute()
    {
        if ($this->date) {
            return $this->date->translatedFormat('j M Y');
        }
        return null;
    }

    public function getFormattedStartedAtAttribute()
    {
        if ($this->started_at) {
            return $this->started_at->translatedFormat('j M Y H:i');
        }
        return "-";
    }

    public function getStatusAttribute()
    {
        if (!$this->created_at) {
            return 'Belum Dimulai';
        }

        $now = now();

        // Ambil waktu mulai dari created_at dan waktu selesai dari relasi schedule->end_time
        $start = $this->created_at;
        $endTime = $this->schedule ? $this->schedule->end_time : null;

        if ($endTime) {
            // Gabungkan tanggal meeting dengan end_time dari schedule
            $end = $this->date->copy()->setTimeFromTimeString($endTime);

            if ($now->between($start, $end)) {
                return 'Sedang Berlangsung';
            } elseif ($now->greaterThan($end)) {
                return 'Telah Berakhir';
            } else {
                return 'Belum Dimulai';
            }
        }

        // Jika tidak ada end_time, fallback ke status default
        return 'Telah Berakhir';
    }
}
