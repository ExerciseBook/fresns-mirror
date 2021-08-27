<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Base\Config\BaseConfig;
use Illuminate\Support\Facades\DB;

class CommonHelper
{
    // Whether https request
    public static function isHttpsRequest()
    {
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) {
            return true;
        }

        return false;
    }

    // Get Domain
    public static function domain()
    {
        $request = request();
        $httpHost = $request->server('HTTP_HOST');

        if (self::isHttpsRequest()) {
            return 'https://'.$httpHost;
        }

        return 'http://'.$httpHost;
    }

    // Get Host
    public static function host()
    {
        $request = request();
        $httpHost = $request->server('HTTP_HOST');

        return $httpHost;
    }

    // Determine if it is a WeChat browser
    public static function isWeChatBrowser()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }

        return false;
    }

    // Remove the requested data
    public static function removeRequestFields($fieldMap)
    {
        foreach ($fieldMap as $field => $arr) {
            request()->offsetUnset($field);
        }
    }

    // Keep only the requested fields
    public static function onlyRequestFields($onlyFieldArr)
    {
        $allFiledMap = request()->all();

        foreach ($allFiledMap as $field => $value) {
            // Remove if not present
            if (! in_array($field, $onlyFieldArr)) {
                request()->offsetUnset($field);
            }
        }
    }

    // object to array
    public static function objectToArray($obj)
    {
        $a = json_encode($obj);
        $b = json_decode($a, true);

        return $b;
    }

    public static function isMobile()
    {
        // If there is HTTP_X_WAP_PROFILE then it must be a mobile device
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // If the via information contains wap then it must be a mobile device (some service providers will block the information)
        if (isset($_SERVER['HTTP_VIA'])) {
            // false if not found, otherwise true
            return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
        }
        // Determine client flags sent by cell phones
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = [
                'mobile',
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
            ];
            // Find mobile browser keywords from HTTP_USER_AGENT
            if (preg_match('/('.implode('|', $clientkeywords).')/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        if (isset($_SERVER['HTTP_ACCEPT'])) {
            // Agreement method (because of the possibility of inaccuracy, put into the final judgment)
            // If it only supports wml and does not support html then it must be a mobile device
            // If wml and html are supported but wml comes before html then it is a mobile device
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }

        return false;
    }

    // Get Web CDN Static
    public static function getWebCdnStatic()
    {
        $domain = CommonHelper::domain();
        $cdnUrl = $domain;

        if (DBHelper::hasTableInCurrentDB('config')) {
            $cdnUrl = DB::table('config')->where('nickname', 'web_cdn_path')->value('content');
        }

        return $cdnUrl;
    }

    // Get H5 CDN Static
    public static function getWebCdnH5Static()
    {
        $domain = CommonHelper::domain();
        $cdnUrl = $domain;

        if (DBHelper::hasTableInCurrentDB('config')) {
            $cdnUrl = DB::table('config')->where('nickname', 'web_cdn_path_h5')->value('content');
        }

        return $cdnUrl;
    }
}
