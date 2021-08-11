<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 系统解耦, 快捷方式入口
namespace App\Http\Fresns\FresnsPlugin;

use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;

class FresnsPluginService extends AmService
{
    //通过unikey获取插件url
    public static function getPluginUrlByUnikey($unikey)
    {
        $plugin = FresnsPlugin::where('unikey', $unikey)->first();
        if (empty($plugin)) {
            return '';
        }
        $uri = $plugin['access_path'];
        if (empty($plugin['plugin_domain'])) {
            $domain = $plugin['plugin_domain'];
        } else {
            $domain = ApiConfigHelper::getConfigByItemKey('backend_domain');
        }
        $url = $domain . $uri;
        return $url;
    }
}
