<?php

namespace App\Models;

class Group extends Model
{
    protected $casts = [
        'permission' => 'array'
    ];

    public function scopeTypeCategory($query)
    {
        return $query->where('type', 1);
    }

    public function scopeTypeGroup($query)
    {
        return $query->where('type', 2);
    }

    public function category()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function groups()
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    public function names()
    {
        return $this->hasMany(Language::class, 'table_id', 'id')
            ->where('table_field', 'name')
            ->where('table_name', 'groups');
    }

    public function descriptions()
    {
        return $this->hasMany(Language::class, 'table_id', 'id')
            ->where('table_field', 'description')
            ->where('table_name', 'groups');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    public function plugin()
    {
        return $this->belongsTo(Plugin::class, 'plugin_unikey', 'unikey');
    }

}
