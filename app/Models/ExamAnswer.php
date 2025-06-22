<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ExamResult;
use App\Models\Question;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAnswer extends Model
{
    protected $fillable = [
        'exam_result_id',
        'question_id',
        'answer',
        'answered_at'
    ];

    public function examResult(): BelongsTo
    {
        return $this->belongsTo(ExamResult::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }
}
