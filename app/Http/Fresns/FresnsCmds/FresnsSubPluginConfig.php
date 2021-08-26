<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsCmds;

use App\Http\Center\Base\BasePluginConfig;
use Illuminate\Validation\Rule;

class FresnsSubPluginConfig extends BasePluginConfig
{
    const SUB_ADD_TABLE_PLUGINS = 'subscribe_plugins';
    const SUBSCRITE_TYPE2 = 2;
    const SUBSCRITE_TYPE3 = 3;
    const SUBSCRITE_TYPE4 = 4;
    const SUBSCRITE_TYPE5 = 5;
    // 扫描指定的订阅信息
    public const PLG_CMD_SUB_ADD_TABLE = 'plg_cmd_sub_add_table';

    // 订阅用户活跃状态
    public const PLG_CMD_SUB_USER_ACTIVE = 'plg_cmd_sub_user_active';
    const PLG_CMD_HANDLE_MAP = [
        self::PLG_CMD_SUB_ADD_TABLE => 'subAddTableHandler',
        self::PLG_CMD_SUB_USER_ACTIVE => 'subUserActiveHandler',
    ];

    public function subAddTableHandlerRule()
    {
        $rule = [
            'tableName' => 'required',
            'insertId' => 'required',
        ];

        return $rule;
    }
}
