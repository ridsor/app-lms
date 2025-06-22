<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'semester',
        'academic_year',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date:d/m/Y',
        'end_date' => 'date:d/m/Y',
        'status' => 'boolean',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['sort'] ?? 'latest', function ($query, $sort) {
            switch ($sort) {
                case 'created_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        });

        $query->when($filters['start_date'] ?? null, function ($query, $start_date) {
            $query->where('start_date', '>=', Carbon::createFromFormat('d/m/Y', $start_date)->format('Y-m-d'));
        });

        $query->when($filters['end_date'] ?? null, function ($query, $end_date) {
            $query->where('end_date', '<=', Carbon::createFromFormat('d/m/Y', $end_date)->format('Y-m-d'));
        });

        $query->when($filters['semester'] ?? null, function ($query, $semester) {
            $query->where('semester', $semester);
        });
    }
}
