<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsExtends;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'extends';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'searchEid' => ['field' => 'content', 'op' => 'LIKE'],
        'searchType' => ['field' => 'extend_type', 'op' => 'LIKE'],
        'searchKey' => ['field' => 'title', 'op' => 'LIKE'],
        'searchMid' => ['field' => 'member_id', 'op' => 'LIKE'],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',

    ];
}
