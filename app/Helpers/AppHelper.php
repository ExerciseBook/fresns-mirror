<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Utilities\ComposerUtility;
use Illuminate\Support\Facades\DB;

class AppHelper
{
    const VERSION = '1.5.0';
    const VERSION_INT = 1;

    // fresns test helper
    public static function fresnsTestHelper()
    {
        //
    }

    // get system info
    public static function getSystemInfo()
    {
        $systemInfo['server'] = php_uname('s').' '.php_uname('r');
        $systemInfo['web'] = $_SERVER['SERVER_SOFTWARE'];

        $phpInfo['version'] = 'PHP '.PHP_VERSION;
        $phpInfo['uploadMaxFileSize'] = ini_get('upload_max_filesize');
        $systemInfo['php'] = $phpInfo;

        return $systemInfo;
    }

    // get mysql database info
    public static function getMySqlInfo()
    {
        $mySqlVersion = 'version()';
        $dbInfo['version'] = 'MySQL '.DB::select('select version()')[0]->$mySqlVersion;

        $dbInfo['timezone'] = 'UTC'.DateHelper::fresnsSqlTimezone();
        $dbInfo['envTimezone'] = config('app.timezone');

        $mySqlCollation = 'Value';
        $dbInfo['collation'] = DB::select('show variables like "collation%"')[1]->$mySqlCollation;

        $mySqlSize = 'Size';
        $dbInfo['sizeMb'] = round(DB::select('SELECT table_schema AS "Database", SUM(data_length + index_length) / 1024 / 1024 AS "Size" FROM information_schema.TABLES GROUP BY table_schema')[1]->$mySqlSize, 2);
        $dbInfo['sizeGb'] = round(DB::select('SELECT table_schema AS "Database", SUM(data_length + index_length) / 1024 / 1024 / 1024 AS "Size" FROM information_schema.TABLES GROUP BY table_schema')[1]->$mySqlSize, 2);

        return $dbInfo;
    }

    // get composer version info
    public static function getComposerVersionInfo()
    {
        $versionInfo = app(ComposerUtility::class)->run(['--version']);

        return $versionInfo;
    }
}
