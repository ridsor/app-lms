<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'meeting_id',
        'user_id',
        'status',
        'edit_by'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function editby(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edit_by');
    }


    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}
