<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingJournal extends Model
{
    protected $table = 'teaching_journals';
    protected $fillable = [
        'meeting_id',
        'subject_matter',
        'sub_subject_matter',
        'additional_note',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
