<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPostMembers;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'post_members';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'post_id' => ['field' => 'post_id', 'op' => '='],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'post_id' => 'post_id',
        'member_id' => 'member_id',
        'plugin_unikey' => 'plugin_unikey',
        'object_id' => 'object_id',
        'more_json' => 'more_json',
    ];
}
