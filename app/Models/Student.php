<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\HomeroomTeacher;
use App\Models\Schedule;
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
        return $this->belongsTo(HomeroomTeacher::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function scopeFilter($query, array $filters)
    {
        // Search global - mencari di semua kolom
        $query->when($filters['search']['value'] ?? false, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')
                    ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        });

        // Filter berdasarkan kelas
        $query->when($filters['class_id'] ?? false, function ($query, $classId) {
            $query->where('class_id', $classId);
        });

        // Filter berdasarkan wali kelas
        $query->when($filters['homeroom_teacher_id'] ?? false, function ($query, $homeroomTeacherId) {
            $query->where('homeroom_teacher_id', $homeroomTeacherId);
        });

        // Filter berdasarkan status
        $query->when($filters['status'] ?? false, function ($query, $status) {
            $query->where('status', $status);
        });

        // Filter berdasarkan jenis kelamin
        $query->when($filters['gender'] ?? false, function ($query, $gender) {
            $query->where('gender', $gender);
        });
    }
}
