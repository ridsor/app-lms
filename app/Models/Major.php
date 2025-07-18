<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    protected $fillable = ['name'];
    public $incrementing = false;

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($major) {
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

            $major->id = $nextId;
        });
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
