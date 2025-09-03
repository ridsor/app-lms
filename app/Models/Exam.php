<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Meeting;
use App\Models\Question;
use App\Models\ExamResult;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mews\Purifier\Casts\CleanHtml;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'description',
        'schedule_id',
        'type',
        'start_time',
        'end_time',
        'duration',
        'exam_mode',
        'is_shuffle_questions',
    ];

    protected $casts = [
        'description' => CleanHtml::class . ':strip_nl,strip_nbsp',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }

    public function results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->orderBy('created_at', "DESC");

        if (!empty($filters['cari'])) {
            $query->whereFullText('title', $filters['cari']);
        }

        $query->whereHas('schedule.period', function ($q) use ($filters) {
            if (!empty($filters['periode'])) {
                $q->where('id', $filters['periode']);
            } else {
                $q->where('status', true);
            }
        });

        if (!empty($filters['kelas'])) {
            $query->whereHas('schedule.class', function ($q) use ($filters) {
                $q->where('name', $filters['kelas']);
            });
        }
        if (!empty($filters['jurusan'])) {
            $query->whereHas('schedule.class.major', function ($q) use ($filters) {
                $q->where('name', $filters['jurusan']);
            });
        }
        if (!empty($filters['tingkat'])) {
            $query->whereHas('schedule.class', function ($q) use ($filters) {
                $q->where('level', $filters['tingkat']);
            });
        }
        if (!empty($filters['mata-pelajaran'])) {
            $query->whereHas('meeting.schedule.subject', function ($q) use ($filters) {
                $q->where('name', $filters['mata-pelajaran']);
            });
        }
        if (!empty($filters['tipe'])) {
            $query->where('type', $filters['tipe']);
        }

        if (!empty($filters['rentang-waktu-dari'])) {
            $query->where('start_time', '>=', $filters['rentang-waktu-dari']);
        };

        if (!empty($filters['rentang-waktu-sampai'])) {
            $query->where('start_time', '<=', $filters['rentang-waktu-sampai']);
        };
    }

    public function scopeFilterByPermission($query, User $user)
    {
        if ($user->can('exam.view')) {
            if ($user->hasRole('operator')) {
                return $query;
            }

            if ($user->hasRole('teacher')) {
                return $query->whereHas('meeting.schedule', function ($q) use ($user) {
                    $q->where('teacher_id', $user->teacher->id);
                });
            }
            if ($user->hasRole('student')) {
                return $query->whereHas('meeting.schedule', function ($q) use ($user) {
                    $q->where('class_id', $user->student->class_id);
                });
            }
            if ($user->hasRole('parent')) {
                return $query->whereHas('meeting.schedule', function ($q) use ($user) {
                    $q->where('class_id', $user->parent->class_id);
                });
            }
        }

        return $query->where('id', 0);
    }
}
