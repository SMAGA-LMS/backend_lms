<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AnnouncementRecipient extends Model
{
    use HasFactory;

    public $table = 'announcement_recipients';

    protected $fillable = [
        'announcement_id',
        'recipient_role',
    ];

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getAnnouncementRecipientsByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table)
            ->join('announcements', 'announcement_recipients.announcement_id', '=', 'announcements.id')
            ->join('users', 'announcements.author_id', '=', 'users.id')
            ->select(
                'announcement_recipients.*',

                'announcements.title as announcement_title',
                'announcements.description as announcement_description',
                'announcements.file as announcement_file',
                'announcements.created_at as announcement_created_at',
                'announcements.updated_at as announcement_updated_at',

                'announcements.author_id as announcement_author_id',
                'users.name as announcement_author_name',
                'users.username as announcement_author_username',
                'users.role as announcement_author_role',
                'users.avatar as announcement_author_avatar',
                'users.created_at as announcement_author_created_at',
                'users.updated_at as announcement_author_updated_at',
            );

        foreach ($conditions as $field => $value) {
            $field = $this->table . '.' . $field;
            if (is_array($value)) {
                $query->where($field, $value[self::OPERATOR], $value[self::VALUE_FIELD]);
            } else {
                $query->where($field, $value);
            }
        }

        return $isCollection ? $query->get() : $query->first();
    }
}
