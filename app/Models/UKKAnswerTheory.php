<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UKKAnswerTheory extends Model
{
    protected $table = 'ukk_answer_theory';

    protected $fillable = [
        'ukk_result_id',
        'questionable_id',
        'questionable_type',
        'answer',
        'answered_at',
        'score',
    ];

    public $timestamps = false;

    public function ukkResult(): BelongsTo
    {
        return $this->belongsTo(UKKResultTheory::class, 'ukk_result_id');
    }

    public function questionable(): BelongsTo
    {
        return $this->morphTo();
    }
}
