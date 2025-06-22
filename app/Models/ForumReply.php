<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DiscussionForum;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumReply extends Model
{
    protected $fillable = [
        'forum_id',
        'created_by',
        'content',
        'edited_at'
    ];

    public function forum(): BelongsTo
    {
        return $this->belongsTo(DiscussionForum::class, 'forum_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
