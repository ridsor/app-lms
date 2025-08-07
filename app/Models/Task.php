<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Meeting;
use App\Models\TaskSubmission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mews\Purifier\Casts\CleanHtml;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'meeting_id',
        'type',
        'file_path',
        'file_size',
        'file_name',
        'start_time',
        'end_time',
        'late_submission_time',
        'allow_late_submission',
        'value_displayed'
    ];

    protected $casts = [
        'description' => CleanHtml::class . ':strip_nl,strip_nbsp',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'late_submission_time' => 'datetime',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function getStatusAttribute(): string
    {
        $now = now();
        if ($this->start_time && $now < $this->start_time) {
            return 'Belum Dimulai';
        } elseif (($this->allow_late_submission && $now > $this->end_time) || ($this->allow_late_submission && $this->late_submission_time && $now < $this->late_submission_time)) {
            return 'Terlambat';
        } elseif ($this->end_time && $now > $this->end_time) {
            return 'Telah Berakhir';
        } else {
            return 'Sedang Berlangsung';
        }
    }

    public function getIsLateSubmissionAllowedAttribute(): bool
    {
        if (!$this->late_submission_time) {
            return $this->allow_late_submission;
        } else {
            return $this->allow_late_submission && $this->late_submission_time && now() < $this->late_submission_time;
        }
    }

    public function getIsLateSubmissionAllowedWithTimeAttribute(): bool
    {
        return $this->allow_late_submission && $this->late_submission_time && now() > $this->end_time && now() < $this->late_submission_time;
    }

    public function scopeFilterByPermission($query, User $user)
    {
        if ($user->can('task.*')) {
            return $query;
        }

        if ($user->can('task.view')) {
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
    }
}
