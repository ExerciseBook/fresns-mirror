<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\FsCmd;

use App\Fresns\Api\Base\Services\BaseService;
use App\Fresns\Api\Center\Common\LogService;
use App\Fresns\Api\Center\Helper\CmdRpcHelper;

/**
 * Class FresnsCrontabPlugin
 * cmd service.
 */
class FresnsSubPluginService
{
    public static function addSubTablePluginItem($tableName, $insertId)
    {
        // Call the plugin to subscribe to the command word
        $cmd = FresnsSubPluginConfig::FRESNS_CMD_SUB_ADD_TABLE;
        $input = [
            'tableName' => $tableName,
            'insertId' => $insertId,
        ];
        LogService::info('table_input', $input);

        $resp = CmdRpcHelper::call(FresnsSubPlugin::class, $cmd, $input);

        return $resp;
    }
}
