<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTerm extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * Get the class_period associated with the academic_term.
     */
    public function classPeriod()
    {
        return $this->hasMany(ClassPeriod::class);
    }
}
