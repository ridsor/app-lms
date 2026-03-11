<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EssayQuestion extends Model
{
    protected $fillable = [
        'question_text',
        'question_points',
        'question_file',
        'questionable_id',
        'questionable_type'
    ];

    public function questionable()
    {
        return $this->morphTo();
    }
}
