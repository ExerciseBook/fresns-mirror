<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsCmd;

use App\Http\Center\Base\BasePlugin;
use App\Http\Center\Base\BasePluginConfig;
use App\Http\Center\Common\LogService;
use App\Http\Center\Helper\PluginHelper;
use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;

/**
 * Class FresnsCrontabPlugin
 * 订阅事件处理.
 */
class FresnsSubPlugin extends BasePlugin
{
    // 构造函数
    public function __construct()
    {
        $this->pluginConfig = new FresnsSubPluginConfig();
        $this->pluginCmdHandlerMap = FresnsSubPluginConfig::PLG_CMD_HANDLE_MAP;
    }

    // 获取错误码
    public function getCodeMap()
    {
        return FresnsSubPluginConfig::CODE_MAP;
    }

    // 扫描指定的订阅信息
    protected function subAddTableHandler($input)
    {
        $tableName = $input['tableName'];
        $insertId = $input['insertId'];
        // 查询订阅信息（tableName是否存在订阅信息）
        $subscribe = FresnsConfigs::where('item_key', FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->where('is_enable',
            1)->first();
        $subscribeArr = '';
        if ($subscribe) {
            $subscribeInfo = json_decode($subscribe['item_value'], true);
            LogService::Info('subscribeInfo', $subscribeInfo);
            if ($subscribeInfo) {
                foreach ($subscribeInfo as $s) {
                    if ($tableName == $s['subscribe_table_name']) {
                        $subscribeArr = $s;
                    }
                }
            }
        }
        // 获取发送命令字得cmd 和 unikey
        $cmd = '';
        $unikey = '';
        if (! empty($subscribeArr)) {
            // 订阅类型为 2， 则执行 anget_plugin_unikey 的 anget_plugin_cmd
            if ($subscribeArr['subscribe_type'] == FresnsSubPluginConfig::SUBSCRITE_TYPE2) {
                $cmd = $subscribeArr['anget_plugin_cmd'];
                $unikey = $subscribeArr['anget_plugin_key'];
            }
            // 订阅类型为3， 则执行 subscribe_plugin_unikey 的 subscribe_plugin_cmd
            if ($subscribeArr['subscribe_type'] == FresnsSubPluginConfig::SUBSCRITE_TYPE3) {
                $cmd = $subscribeArr['subscribe_plugin_cmd'];
                $unikey = $subscribeArr['subscribe_plugin_unikey'];
            }
            // 订阅类型为5， 则执行 subscribe_plugin_unikey 的 subscribe_plugin_cmd
            if ($subscribeArr['subscribe_type'] == FresnsSubPluginConfig::SUBSCRITE_TYPE5) {
                $cmd = $subscribeArr['subscribe_plugin_cmd'];
                $unikey = $subscribeArr['subscribe_plugin_unikey'];
            }
        }
        if (empty($cmd)) {
            return $this->pluginError(BasePluginConfig::CODE_PARAMS_ERROR);
        }
        if (empty($unikey)) {
            return $this->pluginError(BasePluginConfig::CODE_PARAMS_ERROR);
        }
        $pluginClass = PluginHelper::findPluginClass($unikey);
        $input = [
            'tableName' => $tableName,
            'insertId' => $insertId,
        ];
        $resp = PluginRpcHelper::call($pluginClass, $cmd, $input);
        if (PluginRpcHelper::isErrorPluginResp($resp)) {
            return $this->pluginError($resp);
        }

        return $this->pluginSuccess($resp);
    }

    // 订阅用户活跃状态
    protected function subUserActiveHandler($input)
    {
        // 查询订阅信息（tableName是否存在订阅信息）
        $subscribe = FresnsConfigs::where('item_key', FresnsSubPluginConfig::SUB_ADD_TABLE_PLUGINS)->where('is_enable',
            1)->first();
        if (! empty($subscribe)) {
            $subscribeInfo = json_decode($subscribe['item_value'], true);
            if ($subscribeInfo) {
                foreach ($subscribe as $s) {
                    // 订阅类型为4
                    if ($s['subscribe_type'] == FresnsSubPluginConfig::SUBSCRITE_TYPE4) {
                        $cmd = $s['subscribe_plugin_cmd'];
                        $unikey = $s['subscribe_plugin_unikey'];
                        $pluginClass = PluginHelper::findPluginClass($unikey);
                        $input = [];
                        $resp = PluginRpcHelper::call($pluginClass, $cmd, $input);
                        if (PluginRpcHelper::isErrorPluginResp($resp)) {
                            return $this->pluginError($resp);
                        }
                    }
                }
            }
        }

        return $this->pluginSuccess();
    }
}
