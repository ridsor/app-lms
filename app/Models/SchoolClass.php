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
        'major',
        'capacity'
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function scopeFilter($query, array $filters)
    {
        // Search global - mencari di semua kolom
        $query->when($filters['search']['value'] ?? false, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('level', 'like', '%' . $search . '%')
                    ->orWhere('major', 'like', '%' . $search . '%')
                    ->orWhere('capacity', 'like', '%' . $search . '%');
            });
        });

        // Filter berdasarkan level
        $query->when($filters['level'] ?? false, function ($query, $level) {
            $query->where('level', $level);
        });

        // Filter berdasarkan major
        $query->when($filters['major'] ?? false, function ($query, $major) {
            $query->where('major', $major);
        });
    }
}
