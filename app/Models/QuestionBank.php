<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $fillable = ['title', 'subject_id', 'description'];
    public $table = "question_banks";

    public function multipleQuestions()
    {
        return $this->morphMany(MultipleQuestion::class, 'questionable');
    }

    public function essayQuestions()
    {
        return $this->morphMany(EssayQuestion::class, 'questionable');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
