<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionBank extends Model
{
    protected $fillable = ['title', 'subject_id', 'description'];
    public $table = "question_banks";

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
