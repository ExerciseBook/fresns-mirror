<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsEmojis;

use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // Main Table
    const CFG_TABLE = 'emojis';

    // Additional search columns in the main table
    const ADDED_SEARCHABLE_FIELDS = [
        'parent_id' => ['field' => 'parent_id', 'op' => '='],
    ];

    //表情小组
    const TYPE_GROUP = 2;

    // Model Usage - Form Mapping
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'name' => 'name',
        'rank_num' => 'rank_num',
        'is_enable' => 'is_enable',
        'type' => 'type',
        'image_file_id' => 'image_file_id',
        'image_file_url' => 'image_file_url',
        'more_json' => 'more_json',
        'code' => 'code',
        'type' => 'type',
        'parent_id' => 'parent_id',
    ];
}
