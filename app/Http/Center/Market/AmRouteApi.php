<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 插件路由

// 插件基础操作
// Route::group(['prefix' => 'fresns', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Center\Base'], function(){
Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\Center\Base'], function(){

    // 生成插件json描述文件
    Route::get('/plugin/genDescJson', 'PluginController@genDescJson')->name('fresns.plugin.genDescJson');

    // 安装插件
    Route::get('/plugin/install', 'PluginController@install')->name('fresns.plugin.install');

    // 卸载插件
    Route::get('/plugin/uninstall', 'PluginController@uninstall')->name('fresns.plugin.uninstall');

    // 打包插件
    Route::get('/plugin/package', 'PluginController@package')->name('fresns.plugin.package');

    // 升级插件
    Route::get('/plugin/upgrade', 'PluginController@upgrade')->name('fresns.plugin.upgrade');

    // 生成插件json描述文件
    Route::get('/plugin/index', 'IndexController@index')->name('fresns.plugin.index');

});

// 插件后台操作
Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\Center\Market'], function(){

    // 测试
    Route::get('/test/truncatedString', 'TestController@truncatedString')->name('fresns.market.truncatedString');

    // 签名工具
    Route::post('/tools/sign', 'ToolController@sign')->name('admin.tool.sign');
});
