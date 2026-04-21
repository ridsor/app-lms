<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtml;

class UKK extends Model
{
    protected $table = 'ukk';

    protected $fillable = [
        'period_id',
        'operator_id',
        'title',
        'type',
        'major',
        'file_path',
        'file_name',
        'file_size',
        'start_time',
        'end_time',
        'duration',
        'instructions',
        'is_shuffle_questions',
    ];

    protected $casts = [
        'instructions' => CleanHtml::class . ':strip_nl,strip_nbsp',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function results()
    {
        return $this->hasMany(UKKResultTheory::class, 'ukk_id');
    }

    public function multipleQuestions()
    {
        return $this->morphMany(MultipleQuestion::class, 'questionable');
    }

    public function essayQuestions()
    {
        return $this->morphMany(EssayQuestion::class, 'questionable');
    }

    public function getQuestionsAttribute()
    {
        $multiple = $this->multipleQuestions->map(function ($question) {
            $question->setAttribute('question_type', 'multiple');
            return $question;
        });

        $essay = $this->essayQuestions->map(function ($question) {
            $question->setAttribute('question_type', 'essay');
            return $question;
        });

        return $multiple->concat($essay)->sortBy('created_at')->values();
    }

    public function scopeFilter($query, array $filters)
    {
        $query->orderBy('created_at', "DESC");

        if (!empty($filters['cari'])) {
            $query->whereFullText('title', $filters['cari']);
        }

        $query->whereHas('period', function ($q) use ($filters) {
            if (!empty($filters['periode'])) {
                $q->where('id', $filters['periode']);
            } else {
                $q->where('status', true);
            }
        });
        if (!empty($filters['jurusan'])) {
            $query->where('major', $filters['jurusan']);
        }
        if (!empty($filters['tipe'])) {
            $query->where('type', $filters['tipe']);
        }
        if (!empty($filters['rentang-waktu-dari'])) {
            $query->where('start_time', '>=', $filters['rentang-waktu-dari']);
        };
        if (!empty($filters['rentang-waktu-sampai'])) {
            $query->where('start_time', '<=', $filters['rentang-waktu-sampai']);
        };
    }

    public function scopeFilterByPermission($query, User $user)
    {
        if ($user->can('ukk.edit')) {
            return $query;
        }

        if ($user->can('ukk.evaluation')) {
            return $query->where('operator_id', $user->id);
        }

        if ($user->can('ukk.view')) {
            if ($user->hasRole('student')) {
                $student = $user->student;
                return $query->where('major', $student->class->major->name);
            }
            if ($user->hasRole('parent')) {
                $student = $user->parent;
                return $query->where('major', $student->class->major->name);
            }
        }

        return $query->where('id', 0);
    }
}
