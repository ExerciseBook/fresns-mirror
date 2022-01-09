<?php

namespace App\Models;

class Language extends Model
{
    protected $guarded = ['id'];

    public function scopeOfConfig($query)
    {
        return $query->where('table_name', 'configs');
    }

    public function scopeTableName($query, $tableName)
    {
        return $query->where('table_name', $tableName);
    }
}
