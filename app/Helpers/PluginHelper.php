<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use Exception;

class PluginHelper
{
    // 获取插件当前模版
    public static function getCurrTheme($pluginName)
    {
        // todo 获取当前模版，如果没有，则抛出异常
        //  throw new Exception("plugin has not set template");
        return 'theme1';
    }

    // 删除文件夹, 及文件夹下的文件
    public static function deleteDir($dir)
    {

        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != '.' && $file != '..') {
                $fullpath = $dir.'/'.$file;
                if (! is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    self::deleteDir($fullpath);
                }
            }
        }
        closedir($dh);

        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    // 测试环境
    public static function isStaging()
    {
        $appEnv = env('APP_ENV');
        if ($appEnv == 'staging') {
            return true;
        }

        return false;
    }

    // 线上环境
    public static function isProduction()
    {
        $appEnv = env('APP_ENV');
        if ($appEnv == 'production') {
            return true;
        }

        return false;
    }

    // 是否有git
    public static function hasSubGit()
    {
        $subGit = env('SUB_GIT');
        if (StrHelper::isTrue($subGit)) {
            return true;
        }

        return false;
    }
}
