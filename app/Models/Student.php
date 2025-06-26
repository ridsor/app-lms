<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\HomeroomTeacher;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

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
                $q->whereFullText('name', $search);
            });
        });

        // Filter berdasarkan kelas
        $query->when($filters['class'] ?? false, function ($query, $class) {
            $query->whereHas('class', fn($q) => $q->whereFullText('name', $class));
        });

        // Filter berdasarkan jurusan
        $query->when($filters['major'] ?? false, function ($query, $major) {
            $query->whereHas('class', fn($q) => $q->whereHas('major', fn($q) => $q->whereFullText('name', $major)));
        });

        // Filter berdasarkan tingkat
        $query->when($filters['level'] ?? false, function ($query, $level) {
            $query->whereHas('class', fn($q) => $q->where('level', $level));
        });

        // Filter berdasarkan wali kelas
        Log::info($filters);
        $query->when($filters['homeroom_teacher'] ?? false, function ($query, $homeroomTeacher) {
            $query->whereHas('homeroomTeacher', fn($q) => $q->whereHas('teacher', fn($q) => $q->where('name', $homeroomTeacher)));
        });

        // Filter berdasarkan status
        $query->when($filters['status'] ?? false, function ($query, $status) {
            $query->where('status', $status);
        });
    }
}
