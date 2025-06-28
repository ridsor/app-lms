<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class SchoolClass extends Model
{
    use HasFactory;

    protected $table = 'classes';
    protected $fillable = [
        'name',
        'level',
        'major_id',
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function scopeFilter($query, array $filters)
    {

        // Search global - mencari di semua kolom
        $query->when($filters['search']['value'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        });

        // Filter berdasarkan level
        $query->when($filters['level'] ?? false, function ($query, $level) {
            $query->where('level', $level);
        });

        // Filter berdasarkan major
        $query->when($filters['major'] ?? false, function ($query, $major) {
            $query->where('majors.name', $major);
        });
    }
}
