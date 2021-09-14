<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsSessionLogs;

use App\Base\Services\BaseAdminService;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsCmd\FresnsCmdService;
use Illuminate\Support\Facades\Request;

class FsService extends BaseAdminService
{
    public function __construct()
    {
        $this->model = new FsModel();
        $this->resource = FsResource::class;
        $this->resourceDetail = FsResourceDetail::class;
    }

    public function common()
    {
        $common = parent::common();
        return $common;
    }

    // Insert data into the session_logs table
    public static function addSessionLogs(
        $objectName,
        $objectAction,
        $userId = null,
        $memberId = null,
        $objectOrderId = null,
        $uri = null
    ) {
        $deviceInfo = request()->header('deviceInfo');
        // $deviceInfoArr = json_decode($deviceInfo,true);
        // dd(json_encode($deviceInfoArr));
        $platform_id = request()->header('platform');
        $version = request()->header('version');
        $versionInt = request()->header('versionInt');
        $langTag = ApiLanguageHelper::getLangTagByHeader();
        if (empty($platform_id) || empty($version) || empty($versionInt)) {
            return true;
        }

        $map = FsConfig::SESSION_OBJECT_TYPE_ARR;
        $objectType = $map[$objectAction] ?? 1;
        if ($objectType == 15) {
            $objectName = $objectName;
        } else {
            $objectName = Request::getRequestUri();
        }

        $input = [
            'platform_id' => $platform_id,
            'version' => $version,
            'version_int' => $versionInt,
            'lang_tag' => $langTag,
            'object_type' => $objectType,
            'object_name' => $objectName,
            'object_action' => $uri ?? $objectAction,
            'object_result' => 0,
            'object_order_id' => $objectOrderId ?? null,
            'device_info' => $deviceInfo,
            'user_id' => $userId ?? null,
            'member_id' => $memberId ?? null,
        ];

        $id = FresnsSessionLogs::insertGetId($input);
        FresnsCmdService::addSubTablePluginItem(FresnsSessionLogsConfig::CFG_TABLE, $id);

        return $id;
    }

    public static function updateSessionLogs($sessionLogsId, $status, $uid = null, $mid = null, $objectOrderId = null)
    {
        $input['object_result'] = $status;
        if ($uid) {
            $input['user_id'] = $uid;
        }
        if ($mid) {
            $input['member_id'] = $mid;
        }
        if ($objectOrderId) {
            $input['object_order_id'] = $objectOrderId;
        }

        FresnsSessionLogs::where('id', $sessionLogsId)->update($input);
    }

    // Fresns Console (Panel) Add Log
    public static function addConsoleSessionLogs($objectType, $objectAction, $userId = null)
    {
        $fresnsVersion = ApiConfigHelper::getConfigByItemKey('fresns_version');
        $fresnsVersionInt = ApiConfigHelper::getConfigByItemKey('fresns_version_int');

        $input = [
            'platform_id' => '4',
            'version' => $fresnsVersion ?? 1,
            'version_int' => $fresnsVersionInt ?? 1,
            'object_type' => $objectType,
            'object_name' => Request::getRequestUri(),
            'object_action' => $objectAction,
            'object_result' => 0,
            'object_order_id' => $objectOrderId ?? null,
            'device_info' => '[1]',
            'user_id' => $userId ?? null,
            'member_id' => $memberId ?? null,
        ];

        $id = FresnsSessionLogs::insertGetId($input);

        return $id;
    }
}
