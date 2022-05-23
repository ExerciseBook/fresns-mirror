<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class PluginUsage extends Model
{
    use Traits\LangNameTrait;
    use Traits\PluginUsageServiceTrait;

    protected $casts = [
        'data_sources' => 'json',
    ];

    public function plugin()
    {
        return $this->belongsTo(Plugin::class, 'plugin_unikey', 'unikey');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
