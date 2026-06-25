<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UKKResultPractice extends Model
{
    protected $table = 'ukk_result_practice';

    protected $fillable = [
        'ukk_id',
        'student_id',
        'contents',
        'submitted_at',
        'graded_at',
        'score',
        'feedback',
        'graded_by'
    ];

    protected $casts = [
        'contents' => 'array',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function ukk(): BelongsTo
    {
        return $this->belongsTo(UKK::class, 'ukk_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getFormattedScoreAttribute()
    {
        return $this->score;
    }
}
