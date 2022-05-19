<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class PluginCallback extends Model
{
    protected $guarded = [];

    protected $dates = [
        'deleted_at',
    ];

    protected $casts = [
        'content' => 'json',
    ];
}
