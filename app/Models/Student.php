<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $primaryKey = 'user_id';
    public $incrementing = false;

    /**
     * Get the user associated with the student.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the student_enrollment associated with the student.
     */
    public function studentEnrollment()
    {
        return $this->hasMany(StudentEnrollment::class);
    }
}
