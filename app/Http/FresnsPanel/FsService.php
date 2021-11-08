<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsPanel;

use App\Base\Services\BaseAdminService;
use App\Http\UpgradeController;

class FsService extends BaseAdminService
{
    // Get the current setting language
    public static function getLanguage($lang)
    {
        $map = FsConfig::LANGUAGE_MAP;

        return $map[$lang] ?? 'English - English';
    }

    /**
     * version check
     */
    public static function getVersionInfo(){
        $url = 'https://fresns.cn/version.json';
        $rs = file_get_contents($url);
        $api_version =  !empty($rs) ? json_decode($rs,true) : [];
        $current_version = UpgradeController::$version;
        if($api_version){
            $upgrade = version_compare($api_version['version'], $current_version, '>');
            return ['currentVersion'=>$current_version,'canUpgrade'=>$upgrade,'upgradeVersion'=>$api_version['version'],'upgradePackage'=>$api_version['upgradePackage']];
        }else{
            return ['currentVersion'=>$current_version,'canUpgrade'=>false,'upgradeVersion'=>$current_version,'upgradePackage'=>''];
        }
    }
}
