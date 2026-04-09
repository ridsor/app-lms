<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Exam;
use App\Models\Student;
use App\Models\ExamAnswer;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class ExamResult extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'start_time',
        'end_time',
        'score',
        'status',
        'graded_at',
        'graded_by'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'graded_by');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }

    public function getFormattedScoreAttribute()
    {
        if ($this->answers->isEmpty()) {
            return ' - ';
        }

        $value = $this->answers->sum('score');

        if (is_null($value)) {
            return ' - ';
        }

        return $value == (int)$value
            ? (int)$value
            : rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
    }

    public function getRemainingDurationAttribute()
    {
        $now = now();
        $this->end_time;

        if ($this->end_time) {
            return $now->diffInMinutes($this->end_time);
        }

        return 0;
    }
}
