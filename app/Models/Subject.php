<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'curriculum_id',
        'name',
    ];
    public $incrementing = false;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($curriculum) {
            $existingIds = self::pluck('id')->toArray();

            $nextId = null;
            $i = 1;
            while (true) {
                $length = ($i > 99) ? 3 : 2;
                $formattedId = str_pad($i, $length, '0', STR_PAD_LEFT);
                if (!in_array($formattedId, $existingIds)) {
                    $nextId = $formattedId;
                    break;
                }
                $i++;
            }

            $curriculum->id = $nextId;
        });
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search']['value'])) {
            $search = $filters['search']['value'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }
    }
}
