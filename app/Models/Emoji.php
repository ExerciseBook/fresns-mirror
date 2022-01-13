<?php

namespace App\Models;

class Emoji extends Model
{
    protected $table = 'emojis';

    public function scopeGroup($query)
    {
        return $query->where('type', 2);
    }

    public function emojis()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function names()
    {
        return $this->hasMany(Language::class, 'table_id', 'id')
            ->where('table_field', 'name')
            ->where('table_name', 'emojis');
    }
}
