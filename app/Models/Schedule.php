<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Room;
use App\Models\Period;
use App\Models\Meeting;
use App\Models\Grade;
use App\Helpers\Helper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'room_id',
        'period_id',
    ];

    public function class(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(Period::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'schedule_id');
    }

    public function schedule_times(): HasMany
    {
        return $this->hasMany(ScheduleTime::class, 'schedule_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function getStartTimeAttribute($value)
    {
        return Carbon::parse($value)->format('H:i');
    }

    public function getEndTimeAttribute($value)
    {
        return Carbon::parse($value)->format('H:i');
    }

    public function scopeMainFilter($query, array $filters)
    {
        $query->whereHas('period', function ($q) use ($filters) {
            if (!empty($filters['periode'])) {
                $q->where('id', $filters['periode']);
            } else {
                $q->where('status', true);
            }
        });
        if (!empty($filters['kelas'])) {
            $query->whereHas('class', function ($q) use ($filters) {
                $q->where('name', $filters['kelas']);
            });
        }
        if (!empty($filters['jurusan'])) {
            $query->whereHas('class.major', function ($q) use ($filters) {
                $q->where('name', $filters['jurusan']);
            });
        }
        if (!empty($filters['tingkat'])) {
            $query->whereHas('class', function ($q) use ($filters) {
                $q->where('level', $filters['tingkat']);
            });
        }
        if (!empty($filters['mata-pelajaran'])) {
            $query->whereHas('subject', function ($q) use ($filters) {
                $q->where('name', $filters['mata-pelajaran']);
            });
        }
        if (!empty($filters['hari'])) {
            $query->whereHas('schedule_times', function ($q) use ($filters) {
                $q->where('day', Helper::getDayValue($filters['hari']));
            });
        }
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('subjects.name', 'like', '%' . $search . '%');
            });
        }

        $query->when($filters['guru'] ?? false, function ($query, $guru) {
            $query->whereFullText('teachers.name', $guru);
        });
        $query->when($filters['ruangan'] ?? false, function ($query, $ruangan) {
            $query->where('rooms.name', 'like', '%' . $ruangan . '%');
        });
        $query->when($filters['hari'] ?? false, function ($query, $hari) {
            $query->where('day', Helper::getDayValue($hari));
        });
    }

    public function scopeFilterByPermission($query, User $user)
    {
        if ($user->can('schedule.*')) {
            return $query;
        }

        if ($user->can('schedule.view')) {
            if ($user->hasRole('teacher')) {
                return $query->where('teacher_id', $user->teacher->id);
            }
            if ($user->hasRole('student')) {
                return $query->where('class_id', $user->student->class_id)->whereHas('class.students', fn($q) => $q->where('status', 'active'));
            }
            if ($user->hasRole('parent')) {
                return $query->where('class_id', $user->parent->class_id)->whereHas('class.students', fn($q) => $q->where('status', 'active'));
            }
        }
    }
}
