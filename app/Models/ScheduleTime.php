<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

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


    protected $appends = ['formatted_staart_time', 'formatted_end_time'];
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

    public function getFormattedStaartTimeAttribute()
    {
        if ($this->start_time) {
            return $this->start_time->translatedFormat('H:i');
        }
        return null;
    }
    public function getFormattedEndTimeAttribute()
    {
        if ($this->end_time) {
            return $this->end_time->translatedFormat('H:i');
        }
        return null;
    }
}
