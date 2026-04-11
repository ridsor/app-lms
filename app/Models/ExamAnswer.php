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
        'questionable_id',
        'questionable_type',
        'answer',
        'answered_at',
        'score',
    ];

    public $timestamps = false;

    public function examResult(): BelongsTo
    {
        return $this->belongsTo(ExamResult::class);
    }

    public function questionable(): BelongsTo
    {
        return $this->morphTo();
    }
}
