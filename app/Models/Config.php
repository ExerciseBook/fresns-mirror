<?php

namespace App\Models;

class Config extends Model
{
    protected $casts = [
        'item_value' => 'array',
    ];

    public function scopePlatform($query)
    {
        return $query->where('item_key', 'platforms');
    }
}
