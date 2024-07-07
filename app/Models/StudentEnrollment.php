<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    public $table = 'student_enrollments';

    protected $fillable = [
        'user_id',
        'classroom_id',
    ];
}
