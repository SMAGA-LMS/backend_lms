<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeClassroom extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the grade_level associated with the grade_classroom.
     */
    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    /**
     * Get the classroom associated with the grade_classroom.
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Get the class_period associated with the grade_classroom.
     */
    public function classPeriod()
    {
        return $this->hasMany(ClassPeriod::class);
    }
}
