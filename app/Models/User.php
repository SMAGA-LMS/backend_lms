<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the status associated with the user.
     */
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    /**
     * Get the teacher associated with the user.
     */
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    /**
     * Get the student associated with the user.
     */
    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    /**
     * Get the admin associated with the user.
     */
    public function admin()
    {
        return $this->hasOne(Admin::class, 'user_id');
    }
}
