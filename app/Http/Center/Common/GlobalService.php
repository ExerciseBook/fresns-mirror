<?php
/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Center\Common;

use App\Http\Center\Helper\PluginRpcHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsCmd\FresnsCrontablPlugin;
use App\Http\FresnsCmd\FresnsCrontabPluginConfig;
use App\Http\FresnsCmd\FresnsSubPlugin;
use App\Http\FresnsCmd\FresnsSubPluginConfig;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogs;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogsConfig;
use App\Http\FresnsDb\FresnsSessionLogs\FresnsSessionLogsService;
use App\Http\FresnsDb\FresnsUsers\FresnsUsers;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

class GlobalService
{
    // Loading data
    public static function loadData()
    {
        self::initSessionLog();
        self::loadGlobal();
        self::loadGlobalData();
        self::crontabCheck();
    }

    // Initial Configuration
    public static function loadGlobal()
    {
        $fresns = [];

        $uid = request()->header('uid');
        $mid = request()->header('mid');

        $arr = ['platform', 'version', 'versionInt', 'appId', 'uid', 'mid'];
        foreach ($arr as $field) {
            $fresns[$field] = request()->header($field);
        }

        // user and member data
        $fresns['user'] = null;
        $fresns['member'] = null;
        if (! empty($uid)) {
            $user = FresnsUsers::staticFindByField('uuid', $uid);
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

    // Get the value based on key
    public static function getGlobalKey($globalKey)
    {
        return $GLOBALS['fresns'][$globalKey] ?? null;
    }

    // Initialization Log
    public static function initSessionLog()
    {
        $sessionLogInfo = [];
        $deviceInfo = request()->header('deviceInfo');
        $uid = GlobalService::getGlobalKey('user_id');
        $mid = GlobalService::getGlobalKey('member_id');
        if ($deviceInfo) {
            $map = GlobalConfig::URI_CONVERSION_OBJECT_TYPE_NO;
            $uri = Request::getRequestUri();
            $objectType = '';
            foreach ($map as $k => $v) {
                if (in_array($uri, $v)) {
                    $objectType = $k;
                }
            }

            if (! empty($objectType)) {
                $objectName = $uri;
                $objectNameMap = GlobalConfig::URI_CONVERSION_OBJECT_NAME;
                foreach ($objectNameMap as $k => $v) {
                    if (in_array($uri, $v)) {
                        $objectName = $k;
                    }
                }

                $actionMap = GlobalConfig::URI_API_NAME_MAP;
                $uriAction = $actionMap[$uri] ?? 'Unknown';

                $sessionLogInfoId = FresnsSessionLogsService::addSessionLogs($objectName, $objectType, $uid, $mid, null, $uriAction);

                $GLOBALS['session_logs_info']['session_log_id'] = $sessionLogInfoId;
            }
        }
    }

    public static function getGlobalSessionKey($globalKey)
    {
        return $GLOBALS['session_logs_info'][$globalKey] ?? null;
    }

    // Update Log
    public static function updateSessionLog()
    {
        $sessionLogInfo = [];

        $sessionLogTypeUriMap = [
            'type_register' => ['uri1', 'uri2', 'uri3'],
            'type_login' => ['uri6', 'uri4', 'uri5'],
        ];

        // $GLOBALS['session_logs_info'] = $sessionLogInfo;
        // $GLOBALS['session_log_id'] = 3333;
    }

    // Loading config data
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
            config([GlobalConfig::CONFIGS_LIST_API => $arr]);

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

            config([GlobalConfig::CONFIGS_LIST => $map]);
        }
        // config(["lang.{key}_{lang_tag}", "{value}"]);
    }

    /**
     * Timed tasks
     * Perform member role expiration time detection every 10 minutes.
     * Perform user logout tasks every 8 hours.
     * Subscription user daily activity command word.
     */
    public static function crontabCheck()
    {
        // Subscribe to the user's daily activity command word (called only when logged in)
        $uid = request()->header('uid');
        if ($uid) {
            $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_USER_ACTIVE;
            $input = [];
            $resp = PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        }
        $time = date('Y-m-d H:i:s', time());
        $isCheckRole = true;

        // Member role expiration time detection
        $checkRoleTime = FresnsSessionLogs::where('object_name', FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_ROLE_EXPIRED)
        ->where('object_type', FresnsSessionLogsConfig::OBJECT_TYPE_PLUGIN)
        ->orderByDesc('id')
        ->value('created_at');

        // Timed Task Plugin
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
            if ($checkRole > 0) {
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

        // Delete users tasks
        $checkDeleteTime = FresnsSessionLogs::where('object_name', FresnsCrontabPluginConfig::PLG_CMD_CRONTAB_CHECK_DELETE_USER)
        ->where('object_type', FresnsSessionLogsConfig::OBJECT_TYPE_PLUGIN)
        ->orderByDesc('id')
        ->value('created_at');

        if ($checkDeleteTime) {
            if ($checkDelete > 0) {
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
