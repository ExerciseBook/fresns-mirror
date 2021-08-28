<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsCmds;

use App\Http\Center\Base\BasePluginConfig;

class FresnsCrontabPluginConfig extends BasePluginConfig
{
    //新增定时任务
    public const ADD_CRONTAB_PLUGIN_ITEM = 'add_crontab_plugin_item';
    //取消定时任务
    public const DELETE_CRONTAB_PLUGIN_ITEM = 'delete_crontab_plugin_item';
    //新增 订阅信息
    public const ADD_SUB_PLUGIN_ITEM = 'add_sub_plugin_item';
    //删除 订阅信息
    public const DELETE_SUB_PLUGIN_ITEM = 'delete_sub_plugin_item';
    // 每隔 10 分钟执行一次用户角色过期时间检测
    public const PLG_CMD_CRONTAB_CHECK_ROLE_EXPIRED = 'plg_cmd_crontab_check_role_expired';
    // 注销或删除任务
    public const PLG_CMD_CRONTAB_CHECK_DELETE_USER = 'plg_cmd_crontab_check_delete_user';

    // 插件命令字回调映射
    const PLG_CMD_HANDLE_MAP = [
        self::ADD_SUB_PLUGIN_ITEM => 'addSubPluginItemHandler',
        self::DELETE_SUB_PLUGIN_ITEM => 'deleteSubTablePluginItemHandler',
        self::PLG_CMD_CRONTAB_CHECK_ROLE_EXPIRED => 'crontabCheckRoleExpiredHandler',
        self::PLG_CMD_CRONTAB_CHECK_DELETE_USER => 'crontabCheckDeleteUserHandler',
        self::ADD_CRONTAB_PLUGIN_ITEM => 'addCrontabPluginItemHandler',
        self::DELETE_CRONTAB_PLUGIN_ITEM => 'deleteCrontabPluginItemHandler',

    ];

    // 新增订阅信息
    public function addSubPluginItemHandlerRule()
    {
        $rule = [
            'sub_table_plugin_item' => 'required',
        ];

        return $rule;
    }

    // 新增订阅信息
    public function deleteSubPluginItemHandlerRule()
    {
        $rule = [
            'sub_table_plugin_item' => 'required',
        ];

        return $rule;
    }

    //新增定时任务
    public function addCrontabPluginItemHandlerRule()
    {
        $rule = [
            'crontab_plugin_item' => 'required',
        ];

        return $rule;
    }

    //删除定时任务
    public function deleteCrontabPluginItemHandlerRule()
    {
        $rule = [
            'crontab_plugin_item' => 'required',
        ];

        return $rule;
    }
}
