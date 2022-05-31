<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class UserFollow extends Model
{
    const FOLLOW_TYPE_USER = 1;
    const FOLLOW_TYPE_GROUP = 2;
    const FOLLOW_TYPE_HASHTAG = 3;
    const FOLLOW_TYPE_POST = 4;
    const FOLLOW_TYPE_COMMENT = 5;
    
    public function scopeType($query, int $type)
    {
        return $query->where('follow_type', $type);
    }
}
