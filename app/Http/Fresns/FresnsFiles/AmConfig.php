<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsFiles;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'files';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [

    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',

    ];
}
