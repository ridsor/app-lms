<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\AttendanceStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'schedule_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(AttendanceStatus::class);
    }
}
