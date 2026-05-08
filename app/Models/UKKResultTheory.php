<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UKKResultTheory extends Model
{
    protected $table = 'ukk_result_theory';

    protected $fillable = [
        'ukk_id',
        'student_id',
        'score',
        'graded_at',
        'graded_by',
        'status',
    ];

    protected $casts = [
        'graded_at' => 'datetime',
    ];

    public function ukk()
    {
        return $this->belongsTo(UKK::class, 'ukk_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(UKKAnswerTheory::class, 'ukk_result_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function getFormattedScoreAttribute()
    {

        $value = $this->score;

        if (is_null($value)) {
            return ' - ';
        }

        return $value == (int)$value
            ? (int)$value
            : rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
    }

    public function getRemainingSecondsAttribute()
    {
        $now = now();
        $endTime = $this->ukk->end_time;

        if ($now->gt($endTime)) {
            return 0;
        }

        return $now->diffInSeconds($endTime);
    }
}
