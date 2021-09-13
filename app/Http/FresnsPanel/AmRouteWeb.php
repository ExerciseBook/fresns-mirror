<?php

/*
 * Fresns (https://fresns.cn)
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
        // Login Page
        Route::get('/', 'AmControllerWeb@index')->name('fresns.index.index');
    });

    // Login Request (No login status required)
    Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\FresnsPanel'], function () {
        Route::post('/loginAcc', 'AmControllerWeb@loginAcc')->name('fresns.index.loginAcc');
        Route::post('/checkLogin', 'AmControllerWeb@checkLogin')->name('fresns.index.checkLogin');
        Route::get('/login', 'AmControllerWeb@loginIndex')->name('fresns.index.loginIndex');
    });

    // Console Page (login status required)
    Route::group(['prefix' => 'fresns', 'middleware' => ['web', 'auth'], 'namespace' => '\App\Http\FresnsPanel'], function () {
        // Function Operation Page
        Route::get('/dashboard', 'AmControllerWeb@dashboard')->name('fresns.index.dashboard');
        Route::get('/settings', 'AmControllerWeb@settings')->name('fresns.index.settings');
        Route::get('/keys', 'AmControllerWeb@keys')->name('fresns.index.keys');
        Route::get('/admins', 'AmControllerWeb@admins')->name('fresns.index.admins');
        Route::get('/websites', 'AmControllerWeb@websites')->name('fresns.index.websites');
        Route::get('/apps', 'AmControllerWeb@apps')->name('fresns.index.apps');
        Route::get('/plugins', 'AmControllerWeb@plugins')->name('fresns.index.plugins');
        Route::get('/iframe', 'AmControllerWeb@iframe')->name('fresns.index.iframe');
        // Logout Console
        Route::get('/logout', 'AmControllerWeb@login_out')->name('fresns.index.login_out');
        // Setting Language
        Route::post('/setLanguage', 'AmControllerWeb@setLanguage')->name('fresns.index.setLanguage');
        // Console Settings
        Route::post('/updateSetting', 'AmControllerWeb@updateSetting')->name('fresns.index.updateSetting');
        // Administrator Settings
        Route::post('/addAdmin', 'AmControllerWeb@addAdmin')->name('fresns.index.addAdmin');
        Route::post('/delAdmin', 'AmControllerWeb@delAdmin')->name('fresns.index.delAdmin');
        // Key Management
        Route::post('/submitKey', 'AmControllerWeb@submitKey')->name('fresns.index.submitKey');
        Route::post('/updateKey', 'AmControllerWeb@updateKey')->name('fresns.index.updateKey');
        Route::post('/resetKey', 'AmControllerWeb@resetKey')->name('fresns.index.resetKey');
        Route::post('/delKey', 'AmControllerWeb@delKey')->name('fresns.index.delKey');
        // Extensions Related
        Route::post('/install', 'AmControllerWeb@install')->name('fresns.index.install');
        Route::post('/uninstall', 'AmControllerWeb@uninstall')->name('fresns.index.uninstall');
        Route::post('/updateUnikey', 'AmControllerWeb@updateUnikey')->name('fresns.index.updateUnikey');
        Route::post('/localInstall', 'AmControllerWeb@localInstall')->name('fresns.index.localInstall');
        Route::post('/enableUnikeyStatus', 'AmControllerWeb@enableUnikeyStatus')->name('fresns.index.install');
        Route::post('/websiteLinkSubject', 'AmControllerWeb@websiteLinkSubject')->name('fresns.index.websiteLinkSubject');
    });
}
