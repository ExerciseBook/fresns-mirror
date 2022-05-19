<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class GroupAdmin extends Model
{
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
