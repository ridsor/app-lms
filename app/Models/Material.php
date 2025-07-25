<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mews\Purifier\Casts\CleanHtml;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_id',
        'title',
        'description',
        'file_path',
        'file_type'
    ];

    protected $casts = [
        'description' => CleanHtml::class . ':strip_nl,strip_nbsp',
    ];

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }
}
