<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class Plugin extends Model
{
    use Traits\IsEnableTrait;

    protected $guarded = [];

    protected $casts = [
        'scene' => 'array',
    ];

    public function getSceneAttribute($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return $value ?? [];
    }

    public function scopeType($query, $value)
    {
        return $query->where('type', $value);
    }
}
