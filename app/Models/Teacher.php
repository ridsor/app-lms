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
        'gender',
        'date_of_birth',
        'birthplace',
        'religion'
    ];

    protected $casts = [
        'date_of_birth' => 'date:d/m/Y',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomStudents(): HasMany
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

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->whereFullText('teachers.name', $search);
            });
        }
    }
}
