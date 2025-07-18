<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mews\Purifier\Casts\CleanHtml;

class Curriculum extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];
    protected $table = 'curriculums';
    public $incrementing = false;

    protected $casts = [
        'description' => CleanHtml::class . ':strip_nl,strip_nbsp',
    ];

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search']['value'] ?? false, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->whereFullText('name', $search);
            });
        });
    }
}
