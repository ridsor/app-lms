<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'user_id',
        'homeroom_teacher_id',
        'parent_id',
        'name',
        'nis',
        'nisn',
        'date_of_birth',
        'birthplace',
        'gender',
        'religion',
        'admission_year',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date:d/m/Y',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function exam_results(): HasMany
    {
        return $this->hasMany(ExamResult::class);
    }

    public function task_submissions(): HasMany
    {
        return $this->hasMany(TaskSubmission::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function homeroom_teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function scopeFilter($query, array $filters)
    {
        // Search global - fulltext di beberapa kolom utama
        if (!empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->whereFullText('students.name', $search)->orWhere('students.nis', 'like', '%' . $search . '%')->orWhere('students.nisn', 'like', '%' . $search . '%');
            });
        }

        if (!empty($filters['class'])) {
            $query->where('classes.name', $filters['class']);
        }
        if (!empty($filters['major'])) {
            $query->where('majors.name', $filters['major']);
        }
        if (!empty($filters['level'])) {
            $query->where('classes.level', $filters['level']);
        }
        if (!empty($filters['status'])) {
            $query->where('students.status', $filters['status']);
        }
        if (!empty($filters['homeroom_teacher'])) {
            $query->whereFullText('teachers.name', $filters['homeroom_teacher']);
        }
    }

    public function scopeFilterByPermission(Builder $query, User $user): Builder
    {
        // Wakasek dan Admin dapat melihat semua siswa
        if ($user->can('student.*')) {
            return $query;
        }


        // Teacher hanya dapat melihat siswa yang diwalinya
        if ($user->can('student.view.homeroomteacher') && $user->teacher) {
            return $query->where('students.homeroom_teacher_id', $user->teacher->id);
        }

        // Return empty query jika tidak punya permission
        return $query->where('id', 0);
    }
}
