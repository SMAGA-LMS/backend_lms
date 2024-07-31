<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassPeriod extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * Get the grade_classroom associated with the class_period.
     */
    public function gradeClassroom()
    {
        return $this->belongsTo(GradeClassroom::class);
    }

    /**
     * Get the academic_term associated with the class_period.
     */
    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    /**
     * Get the teacher associated with the class_period.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Get the student_enrollment associated with the class_period.
     */
    public function studentEnrollment()
    {
        return $this->hasMany(StudentEnrollment::class);
    }
}
