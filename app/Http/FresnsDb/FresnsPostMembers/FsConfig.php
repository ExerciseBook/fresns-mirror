<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPostMembers;

use App\Base\Config\BaseConfig;

class FsConfig extends BaseConfig
{
    // Main Table
    const CFG_TABLE = 'post_members';

    // Additional search columns in the main table
    const ADDED_SEARCHABLE_FIELDS = [
        'post_id' => ['field' => 'post_id', 'op' => '='],
    ];

    // Model Usage - Form Mapping
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'post_id' => 'post_id',
        'member_id' => 'member_id',
        'plugin_unikey' => 'plugin_unikey',
        'object_id' => 'object_id',
        'more_json' => 'more_json',
    ];
}
