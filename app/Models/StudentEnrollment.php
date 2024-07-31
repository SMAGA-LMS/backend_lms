<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * Get the class_period associated with the student_enrollment.
     */
    public function classPeriod()
    {
        return $this->belongsTo(ClassPeriod::class);
    }

    /**
     * Get the student associated with the student_enrollment.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
