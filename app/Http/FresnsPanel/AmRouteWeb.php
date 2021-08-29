<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

use App\Http\Center\Common\GlobalService;
use App\Http\FresnsApi\Base\FresnsBaseApiController;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;

$appName = env('APP_NAME');

if ($appName == 'Fresns') {
    GlobalService::loadGlobalData();
    $adminPath = ApiConfigHelper::getConfigByItemKey(FresnsConfigsConfig::BACKEND_PATH) ?? 'admin';

    $adminPath = '/fresns'."/$adminPath";
    Route::group(['prefix' => "$adminPath", 'namespace' => '\App\Http\FresnsPanel'], function () {
        //首页
        Route::get('/', 'AmControllerWeb@index')->name('fresns.index.index');
    });

    //不用登录的路由
    Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\FresnsPanel'], function () {
        Route::post('/loginAcc', 'AmControllerWeb@loginAcc')->name('fresns.index.loginAcc');
        Route::post('/checkLogin', 'AmControllerWeb@checkLogin')->name('fresns.index.checkLogin');
        Route::get('/login', 'AmControllerWeb@loginIndex')->name('fresns.index.loginIndex');
    });

    //页面需登录接口
    Route::group(['prefix' => 'fresns', 'middleware' => ['web', 'auth'], 'namespace' => '\App\Http\FresnsPanel'], function () {
        //页面
        Route::get('/dashboard', 'AmControllerWeb@dashboard')->name('fresns.index.dashboard');
        Route::get('/websites', 'AmControllerWeb@websites')->name('fresns.index.websites');
        Route::get('/admins', 'AmControllerWeb@admins')->name('fresns.index.admins');
        Route::get('/apps', 'AmControllerWeb@apps')->name('fresns.index.apps');
        Route::get('/plugins', 'AmControllerWeb@plugins')->name('fresns.index.plugins');
        Route::get('/iframe', 'AmControllerWeb@iframe')->name('fresns.index.iframe');
        Route::get('/keys', 'AmControllerWeb@keys')->name('fresns.index.keys');
        Route::get('/settings', 'AmControllerWeb@settings')->name('fresns.index.settings');
        //退出登陆
        Route::get('/logout', 'AmControllerWeb@login_out')->name('fresns.index.login_out');
        //设置多语言
        Route::post('/setLanguage', 'AmControllerWeb@setLanguage')->name('fresns.index.setLanguage');
        Route::get('/getPostPage', 'AmControllerApi@getPostPage')->name('solution.index.getPostPage');
        //新增管理员
        Route::post('/addAdmin', 'AmControllerWeb@addAdmin')->name('fresns.index.addAdmin');
        //删除管理员
        Route::post('/delAdmin', 'AmControllerWeb@delAdmin')->name('fresns.index.delAdmin');
        //保存设置
        Route::post('/updateSetting', 'AmControllerWeb@updateSetting')->name('fresns.index.updateSetting');
        //重置密钥
        Route::post('/resetKey', 'AmControllerWeb@resetKey')->name('fresns.index.resetKey');
        //新增密钥
        Route::post('/submitKey', 'AmControllerWeb@submitKey')->name('fresns.index.submitKey');
        //编辑密钥
        Route::post('/updateKey', 'AmControllerWeb@updateKey')->name('fresns.index.updateKey');
        //启用禁用密钥
        Route::post('/enableStatus', 'AmControllerWeb@enableStatus')->name('fresns.index.enableStatus');
        //删除
        Route::post('/delKey', 'AmControllerWeb@delKey')->name('fresns.index.delKey');
        //卸载插件
        Route::post('/uninstall', 'AmControllerWeb@uninstall')->name('fresns.index.uninstall');
        //安装插件
        Route::post('/install', 'AmControllerWeb@install')->name('fresns.index.install');
        Route::post('/updateUnikey', 'AmControllerWeb@updateUnikey')->name('fresns.index.updateUnikey');
        Route::post('/localInstall', 'AmControllerWeb@localInstall')->name('fresns.index.localInstall');
        Route::post('/enableUnikeyStatus', 'AmControllerWeb@enableUnikeyStatus')->name('fresns.index.install');
        Route::post('/websiteLinkSubject', 'AmControllerWeb@websiteLinkSubject')->name('fresns.index.websiteLinkSubject');
    });
}
