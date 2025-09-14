<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleStudent extends Model
{
    protected $fillable = [
        'student_id',
        'schedule_ids'
    ];

    public $table = 'schedule_students';

    protected $casts = [
        'schedule_ids' => 'array',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
