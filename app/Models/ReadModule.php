<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadModule extends Model
{
    use HasFactory;

    public $table = 'read_modules';

    protected $fillable = [
        'user_id',
        'module_id',
    ];
}
