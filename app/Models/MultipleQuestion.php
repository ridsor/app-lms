<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MultipleQuestion extends Model
{
    protected $fillable = [
        'question_text',
        'option_a_image',
        'option_b_image',
        'option_c_image',
        'option_d_image',
        'option_e_image',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'correct_answer',
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
