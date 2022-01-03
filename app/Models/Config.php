<?php

namespace App\Models;

class Config extends Model
{
    public function getItemValueAttribute($value)
    {
        if ($this->item_type == 'array') {
            return json_decode($value, true);
        }

        return $value;
    }

    public function scopePlatform($query)
    {
        return $query->where('item_key', 'platforms');
    }
}
