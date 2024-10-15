<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    public $table = 'attendances';

    protected $fillable = [
        'student_id',
        'classenrollment_id',
        'date_time',
        'session',
    ];
}
