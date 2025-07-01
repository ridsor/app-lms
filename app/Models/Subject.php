<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_id',
        'subject_code',
        'subject_name',
        'category',
        'grade_level',
        'major_id',
        'description'
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }
}
