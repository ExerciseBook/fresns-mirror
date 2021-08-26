<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsUserWalletLogs;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'user_wallet_logs';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'user_id' => ['field' => 'user_id', 'op' => '='],
        'type' => ['field' => 'object_type', 'op' => '='],
        'status' => ['field' => 'is_enable', 'op' => '='],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',

    ];
}
