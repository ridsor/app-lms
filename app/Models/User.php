<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Teacher;
use App\Models\Student;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'image'
    ];

    protected $with = ['student', 'teacher', 'parent'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->username)) {
                $user->username = self::generateUsername($user->name);
            }
            if (empty($user->password)) {
                $user->password = bcrypt($user->username);
            }
        });
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function parent()
    {
        return $this->hasOne(Student::class, 'parent_id');
    }

    public function form_replies()
    {
        return $this->hasMany(ForumReply::class);
    }

    public function discussion_forums()
    {
        return $this->hasMany(DiscussionForum::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }


    /**
     * Generate unique username dari nama, format: slug_nama_4karakteracak
     * Contoh: ryan_syukur_b3s4
     *
     * @param string $name
     * @return string
     */
    public static function generateUsername($name)
    {
        $base = strtolower(substr(strtok($name, " "), 0, 4));
        do {
            $random = strtolower(Str::random(5));
            $username = $base  . $random;
        } while (self::where('username', $username)->exists());
        return $username;
    }
}
