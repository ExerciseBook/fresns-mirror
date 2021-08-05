<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsCmds;

use App\Base\Services\BaseService;
use  App\Http\Share\Common\LogService;
use App\Http\Center\Helper\PluginRpcHelper;

/**
 * Class FresnsCrontabPlugin
 * cmd service
 *
 * @package App\Http\Fresns\FresnsCmds
 */
class FresnsCmdService
{
    public static function addSubTablePluginItem($tableName, $insertId)
    {
        // 调用插件订阅命令字
        $cmd = FresnsSubPluginConfig::PLG_CMD_SUB_ADD_TABLE;
        $input = [
            'tableName' => $tableName,
            'insertId' => $insertId,
        ];
        LogService::info('table_input', $input);
        // dd($input);
        $resp = PluginRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);
        return $resp;
    }


}