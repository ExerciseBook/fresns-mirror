<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsMembers;

use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // Main Table
    const CFG_TABLE = 'members';

    // Additional search columns in the main table
    const ADDED_SEARCHABLE_FIELDS = [

    ];

    // Model Usage - Form Mapping
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'name' => 'name',
        'is_enable' => 'is_enable',
        'type' => 'type',
        'icon_file_id' => 'icon_file_id',
        'icon_file_url' => 'icon_file_url',
        'is_display_name' => 'is_display_name',
        'is_display_icon' => 'is_display_icon',
        'nickname_color' => 'nickname_color',
        'permission' => 'permission',

    ];

    const TYPE_OPTION = [
        ['key' => 1, 'text' => '管理人员类'],
        ['key' => 2, 'text' => '系统设置类'],
        ['key' => 3, 'text' => '用户运营类'],
    ];
}
