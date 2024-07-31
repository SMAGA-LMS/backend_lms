<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * Get the grade_classroom associated with the grade_level.
     */
    public function gradeClassroom()
    {
        return $this->hasMany(GradeClassroom::class);
    }
}
