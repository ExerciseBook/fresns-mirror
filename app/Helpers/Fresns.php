<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists(__DIR__.'/../storage/framework/maintenance.php')) {
    require __DIR__.'/../storage/framework/maintenance.php';
}
// 框架初始化
require_once __DIR__.'/../../vendor/autoload.php';
$app = require_once __DIR__.'/../../bootstrap/app.php';
$kernel = $app->make(Kernel::class);

tap($kernel->handle(
    $request = Request::capture()
));

if( !function_exists('fresns_config_set')){
    // 设置配置值
    function fresns_config_set($itemKey, $columnName ,$columnValue){
        \App\Helpers\FresnsConfig::configSet($itemKey, $columnName,$columnValue);
    }
}

if( !function_exists('fresns_config_get')){
    // 获取配置值
    function fresns_config_get($itemKey,$columnName){
        return \App\Helpers\FresnsConfig::configGet($itemKey,$columnName);
    }
}

if( !function_exists('fresns_config_set_with_lang')){
    // 获取配置值
    function fresns_config_set_with_lang($itemKey,$content,$lang){
        return \App\Helpers\FresnsConfig::configLangSet($itemKey,$content,$lang);
    }
}

if( !function_exists('fresns_config_get_with_lang')){
    // 获取配置值
    function fresns_config_get_with_lang($itemKey,$lang){
        return \App\Helpers\FresnsConfig::configLangGet($itemKey,$lang);
    }
}
