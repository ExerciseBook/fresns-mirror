<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Share\AmGlobal;

use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsCmds\FresnsCrontablPlugin;
use App\Http\Fresns\FresnsCmds\FresnsCrontabPluginConfig;
use App\Http\Fresns\FresnsCmds\FresnsSubPlugin;
use App\Http\Fresns\FresnsCmds\FresnsSubPluginConfig;
use App\Http\Fresns\FresnsConfigs\FresnsConfigs;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogsConfig;
use App\Http\Fresns\FresnsSessionLogs\FresnsSessionLogsService;
use App\Http\Fresns\FresnsUsers\FresnsUsers;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

class AmService
{
    // 加载数据
    public static function loadData()
    {
        self::initSessionLog();
        self::loadGlobal();
        self::loadGlobalData();
        self::crontabCheck();
    }

    // 初始化配置
    public static function loadGlobal()
    {
        $fresns = [];

        $uid = request()->header('uid');
        $mid = request()->header('mid');

        $arr = ['platform', 'version', 'versionInt', 'appId', 'uid', 'mid'];
        foreach ($arr as $field) {
            $fresns[$field] = request()->header($field);
        }

        // user 和 member 值
        $fresns['user'] = null;
        $fresns['member'] = null;
        if (! empty($uid)) {
            $user = FresnsUsers::staticFindByField('uuid', $uid);
            // dd($user);
            $fresns['user'] = $user ?? null;
            $fresns['user_id'] = $user->id ?? null;
        }

        if (! empty($mid)) {
            $member = FresnsMembers::staticFindByField('uuid', $mid);
            $fresns['member'] = $member ?? null;
            $fresns['member_id'] = $member->id ?? null;
        }

        $langTag = ApiLanguageHelper::getLangTagByHeader();
        $fresns['langTag'] = $langTag;

        $GLOBALS['fresns'] = $fresns;
    }

    // 根据 key 获取值
    public static function getGlobalKey($globalKey)
    {
        return $GLOBALS['fresns'][$globalKey] ?? null;
    }

    // 初始化
    public static function initSessionLog()
    {
        $sessionLogInfo = [];
        $deviceInfo = request()->header('deviceInfo');
        $uid = GlobalService::getGlobalKey('user_id');
        $mid = GlobalService::getGlobalKey('member_id');
        if ($deviceInfo) {
            $map = AmConfig::URI_CONVERSION_OBJECT_TYPE;
            $uri = Request::getRequestUri();
            $objectType = '';
            foreach ($map as $k => $v) {
                if (in_array($uri, $v)) {
                    $objectType = $k;
                }
            }

            if (! empty($objectType)) {
                $objectName = $uri;
                $objectNameMap = AmConfig::URI_CONVERSION_OBJECT_NAME;
                foreach ($objectNameMap as $k => $v) {
                    if (in_array($uri, $v)) {
                        $objectName = $k;
                    }
                }

                $actionMap = AmConfig::URI_API_NAME_MAP;
                $uriAction = $actionMap[$uri] ?? '未知';

                $sessionLogInfoId = FresnsSessionLogsService::addSessionLogs($objectName, $objectType, $uid, $mid, null, $uriAction);

                $GLOBALS['session_logs_info']['session_log_id'] = $sessionLogInfoId;
            }
        }
    }

    public static function getGlobalSessionKey($globalKey)
    {
        return $GLOBALS['session_logs_info'][$globalKey] ?? null;
    }

    // 更新
    public static function updateSessionLog()
    {
        $sessionLogInfo = [];

        $sessionLogTypeUriMap = [
            'type_register' => ['uri1', 'uri2', 'uri3'],
            'type_login' => ['uri6', 'uri4', 'uri5'],
        ];

        // $GLOBALS['session_logs_info'] = $sessionLogInfo;

        // // 插入操作
        // $GLOBALS['session_log_id'] = 3333;
    }

    /**
     * 加载配置数据.
     */
    public static function loadGlobalData()
    {
        $hasConfig = Schema::hasTable(FresnsConfigsConfig::CFG_TABLE);
        if ($hasConfig) {
            $itemArr = FresnsConfigs::get()->toArray();
            $arr = [];
            foreach ($itemArr as $v) {
                $item = [];
                $item['item_key'] = $v['item_key'];
                $item['item_tag'] = $v['item_tag'];
                $item['item_type'] = $v['item_type'];
                $item['item_value'] = $v['item_value'];
                if ($v['item_tag'] == 'checkbox' || $v['item_type'] == 'select') {
                    if (strstr($item['item_value'], ',')) {
                        $item['item_value'] = explode(',', $v['item_value']);
                    }
                }
                if ($v['item_tag'] != 'file') {
                    if ($v['item_value'] == 'true') {
                        $item['item_value'] = true;
                    }
                    if ($v['item_value'] == 'false') {
                        $item['item_value'] = false;
                    }
                }
                $item['is_restful'] = $v['is_restful'];
                $item['is_multilingual'] = $v['is_multilingual'];
                $item['is_enable'] = $v['is_enable'];
                $arr[] = $item;
            }
            config([AmConfig::CONFIGS_LIST_API => $arr]);

            $mapArr = [];
            foreach ($arr as $v) {
                $mapArr[$v['item_tag']][] = $v;
            }

            $map = [];
            foreach ($mapArr as $k => $v) {
                $it = [];
                foreach ($v as $value) {
                    $it[$value['item_key']] = $value['item_value'];
                    $map[$k] = $it;
                }
            }
            $languageStatus = FresnsConfigs::where('item_key', FresnsConfigsConfig::LANGUAGE_STATUS)->where('is_restful', 1)->value('item_value');
            $langSettings = FresnsConfigs::where('item_key', FresnsConfigsConfig::LANG_SETTINGS)->where('is_restful', 1)->value('item_value');
            $langSettingsArr = json_decode($langSettings, true);
            $default = ApiLanguageHelper::getDefaultLanguageByApi();

            $lang['language_status'] = empty($languageStatus) ? null : boolval($languageStatus);
            $lang['default_language'] = $default;
            $lang['language_menus'] = $langSettingsArr;
            if (! empty($lang['language_status']) || ! empty($lang['default_language']) || ! empty($lang['language_menus'])) {
                $map['language'] = $lang;
            }

            config([AmConfig::CONFIGS_LIST => $map]);
        }
        // config(["lang.{key}_{lang_tag}", "{value}"]);
    }

    /**
     * 定时任务
     * 每隔 10 分钟执行一次用户角色过期时间检测
     * 每隔 8 小时执行一次用户注销任务
     * 订阅用户日活命令字.
     */
    public static function crontabCheck()
    {
        //订阅用户日活命令字 登陆才调用
        $uid = request()->header('uid');
        if ($uid) {
            $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_USER_ACTIVE;
            $input = [];
            $resp = PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        }
        $time = date('Y-m-d H:i:s', time());
        $isCheckRole = true;
        //用户角色过期时间检测
        $checkRoleTime = FresnsSessionLogs::where('object_name', FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_ROLE_EXPIRED)
        ->where('object_type', FresnsSessionLogsConfig::OBJECT_TYPE_PLUGIN)
        ->orderByDesc('id')
        ->value('created_at');

        $crontabPlugins = ApiConfigHelper::getConfigByItemKey('crontab_plugins');
        $checkRole = null;
        $checkDelete = null;
        if ($crontabPlugins) {
            $crontabPluginsArr = json_decode($crontabPlugins, true);
            foreach ($crontabPluginsArr as $v) {
                if ($v['crontab_plugin_cmd'] == FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_ROLE_EXPIRED) {
                    $checkRole = $v['crontab_task_period'];
                }
                if ($v['crontab_plugin_cmd'] == FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_DELETE_USER) {
                    $checkDelete = $v['crontab_task_period'];
                }
            }
        }

        if ($checkRoleTime) {
            if ($checkRole >= 0) {
                $checkRoleExpiredAt = date('Y-m-d H:i:s', strtotime("+$checkRole min", strtotime($checkRoleTime)));
                if ($checkRoleExpiredAt > $time) {
                    $isCheckRole = false;
                }
            } else {
                $isCheckRole = false;
            }
        }

        if ($isCheckRole == true) {
            $cmd = FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_ROLE_EXPIRED;
            $input = [];
            $resp = PluginRpcHelper::call(FresnsCrontablPlugin::class, $cmd, $input);
        }
        $isCheckDelete = true;
        //用户注销任务
        $checkDeleteTime = FresnsSessionLogs::where('object_name', FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_DELETE_USER)
        ->where('object_type', FresnsSessionLogsConfig::OBJECT_TYPE_PLUGIN)
        ->orderByDesc('id')
        ->value('created_at');

        if ($checkDeleteTime) {
            if ($checkDelete >= 0) {
                $checkDeleteExpiredAt = date('Y-m-d H:i:s', strtotime('+8 hours', strtotime($checkDeleteTime)));
                if ($checkDeleteExpiredAt > $time) {
                    $isCheckDelete = false;
                }
            } else {
                $isCheckDelete = false;
            }
        }

        if ($isCheckDelete == true) {
            $cmd = FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_DELETE_USER;
            $input = [];
            $resp = PluginRpcHelper::call(FresnsCrontablPlugin::class, $cmd, $input);
        }
    }
}
