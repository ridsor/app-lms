<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'nis',
        'nisn',
        'class_id',
        'homeroom_teacher_id',
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
        return $this->belongsTo(User::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'homeroom_teacher_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function scopeFilter($query, array $filters)
    {
        // Search global - fulltext di beberapa kolom utama
        if (!empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->whereFullText('students.name', $search);
            });
        }

        if (!empty($filters['class'])) {
            $query->whereFullText('classes.name', $filters['class']);
        }
        if (!empty($filters['major'])) {
            $query->whereFullText('majors.name', $filters['major']);
        }
        if (!empty($filters['level'])) {
            $query->where('classes.level', $filters['level']);
        }
        if (!empty($filters['status'])) {
            $query->where('students.status', $filters['status']);
        }
    }
}
