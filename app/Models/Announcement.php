<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Announcement extends Model
{
    use HasFactory;

    public $table = 'announcements';

    protected $fillable = [
        'title',
        'description',
        'file',
        'author_id',
    ];

    public function insertNewAnnouncement(array $data)
    {
        $data['created_at'] = now();
        return DB::table($this->table)->insertGetId($data);
    }

    public function getAnnouncementByID($id)
    {
        return DB::table($this->table)
            ->join('users', 'announcements.author_id', '=', 'users.id')
            ->select(
                'announcements.*',

                'users.name as author_name',
                'users.username as author_username',
                'users.role as author_role',
                'users.avatar as author_avatar',
                'users.created_at as author_created_at',
                'users.updated_at as author_updated_at',
            )
            ->where('announcements.id', $id)
            ->first();
    }
}
