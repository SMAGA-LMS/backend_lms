<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * Get the user associated with the teacher.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the class_period associated with the teacher.
     */
    public function classPeriod()
    {
        return $this->hasMany(ClassPeriod::class);
    }
}
