<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UKK extends Model
{
    protected $table = 'ukk';

    protected $fillable = [
        'period_id',
        'title',
        'type',
        'major',
        'start_time',
        'end_time',
        'duration',
        'instructions',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
