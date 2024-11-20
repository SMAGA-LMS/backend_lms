<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    public $table = 'modules';

    protected $fillable = [
        'name',
        'description',
        'file',
        // 'course_id',  // read database/migrations/2024_08_07_130656_create_modules_table.php
    ];
}
