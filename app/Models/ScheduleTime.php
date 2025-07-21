<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'day',
        'meeting_method',
        'start_time',
        'end_time'
    ];
    public $timestamps = false;


    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function meetings()
    {
        return $this->belongsTo(Schedule::class, 'schedule_time_id');
    }
}
