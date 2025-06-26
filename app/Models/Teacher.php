<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Schedule;
use App\Models\AssignmentSubmission;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'nip',
        'specialization',
        'education_level',
        'date_of_birth',
        'birthplace',
        'religion'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomTeacher(): HasMany
    {
        return $this->hasMany(Student::class, 'homeroom_teacher_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'graded_by');
    }
}
