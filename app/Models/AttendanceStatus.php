<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceStatus extends Model
{
    protected $fillable = [
        'attendance_id',
        'status',
        'edit_by'
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edit_by');
    }
}
