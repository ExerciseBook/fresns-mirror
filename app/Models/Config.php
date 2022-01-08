<?php

namespace App\Models;

class Config extends Model
{
    public function getItemValueAttribute($value)
    {
        if (in_array($this->item_type,  ['array', 'plugins'])) {
            return json_decode($value, true);
        }

        return $value;
    }

    public function setItemValueAttribute($value)
    {
        if ($this->item_type == 'array' || is_array($value)) {
            $value = json_encode($value);
        }
        $this->attributes['item_value'] = $value;
    }

    public function scopePlatform($query)
    {
        return $query->where('item_key', 'platforms');
    }

    public function setDefaultValue()
    {
        if ($this->item_type == 'boolean') {
            $this->item_value = 'false';
        }  else if ($this->item_type == 'number') {
            $this->item_value = 0;
        } else {
            $this->item_value = NULl;
        }

        return $this;
    }
}
