<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Meeting;
use App\Models\Question;
use App\Models\ExamResult;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    protected $fillable = [
        'title',
        'description',
        'schedule_id',
        'type',
        'start_date',
        'end_date',
        'duration',
        'exam_mode',
        'display_status',
        'is_shuffle_questions',
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
}
