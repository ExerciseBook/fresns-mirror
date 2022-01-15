<?php

namespace App\Models;

class Plugin extends Model
{
    protected $casts = [
        'scene' => 'array'
    ];

    public function scopeType($query, $value)
    {
        return $query->where('type', $value);
    }
}
