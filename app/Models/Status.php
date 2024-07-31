<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    /**
     * Get the user associated with the status.
     */
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
