<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Classroom extends Model
{
    use HasFactory;

    public $table = 'classrooms';

    protected $fillable = [
        'name',
        'grade',
    ];

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getClassroomsByCondition(array $conditions = [], $isCollection = true)
    {
        $conditions = array_filter($conditions);
        $query = DB::table($this->table);

        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $query->where($field, $value[self::OPERATOR], $value[self::VALUE_FIELD]);
            } else {
                $query->where($field, $value);
            }
        }

        return $isCollection ? $query->get() : $query->first();
    }

    public function insertNewClassroom(array $data)
    {
        $data['created_at'] = now();
        return DB::table($this->table)->insertGetId($data);
    }

    public function getClassroomByID($id)
    {
        return DB::table($this->table)->where('id', $id)->first();
    }
}
