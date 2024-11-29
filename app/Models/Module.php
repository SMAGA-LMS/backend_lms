<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    const OPERATOR = 0;
    const VALUE_FIELD = 1;
    public function getModulesByCondition(array $conditions = [], $isCollection = true)
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

    public function insertNewModule(array $data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function getModuleByID($id)
    {
        return DB::table($this->table)->where('id', $id)->first();
    }
}
