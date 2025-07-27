<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class MeetingText extends Model
{
    protected $fillable = ['meeting_id', 'text'];

    protected $casts = [
        'text' => CleanHtml::class . ':strip_nl,strip_nbsp',
    ];

    public function meeting()
    {
        return $this->belongsTo(Meeting::class);
    }
}
