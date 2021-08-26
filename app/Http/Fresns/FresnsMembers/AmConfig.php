<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsMembers;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'members';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [

    ];

    // model 使用 - 表单映射
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
