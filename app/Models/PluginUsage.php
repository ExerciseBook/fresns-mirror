<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models;

class PluginUsage extends Model
{
    // todo: 完善类型：@see https://fresns.cn/database/plugins/plugin-usages.html
    const TYPE_CONTENT = 4;
    
    use Traits\LangNameTrait;
    use Traits\PluginUsageServiceTrait;

    protected $casts = [
        'data_sources' => 'json',
    ];

    public function scopeType($query, int $type)
    {
        return $query->where('type', $type);
    }

    public function scopeIsEnable($query, bool $isEnable = true)
    {
        return $query->where('is_enable', $isEnable);
    }

    public function plugin()
    {
        return $this->belongsTo(Plugin::class, 'plugin_unikey', 'unikey');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
