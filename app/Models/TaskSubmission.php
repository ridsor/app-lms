<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskSubmission extends Model
{
    protected $fillable = [
        'task_id',
        'student_id',
        'contents',
        'submitted_at',
        'graded_at',
        'group_members',
        'score',
        'feedback',
        'graded_by'
    ];

    protected $casts = [
        'contents' => 'array',
        'group_members' => 'array',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'graded_by');
    }

    public function getFormattedScoreAttribute()
    {
        $value = $this->score;
        return $value == (int)$value
            ? (int)$value
            : rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
    }
}
