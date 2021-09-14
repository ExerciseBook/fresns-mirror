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
        Route::get('/', 'AmControllerWeb@index')->name('admin.fresnsConsole.index');
    });

    // Login Request (No login status required)
    Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\FresnsPanel'], function () {
        Route::post('/loginAcc', 'AmControllerWeb@loginAcc')->name('admin.fresnsConsole.loginAcc');
        Route::post('/checkLogin', 'AmControllerWeb@checkLogin')->name('admin.fresnsConsole.checkLogin');
        Route::get('/login', 'AmControllerWeb@loginIndex')->name('admin.fresnsConsole.loginIndex');
    });

    // Console Page (login status required)
    Route::group(['prefix' => 'fresns', 'middleware' => ['web', 'auth'], 'namespace' => '\App\Http\FresnsPanel'], function () {
        // Function Operation Page
        Route::get('/dashboard', 'AmControllerWeb@dashboard')->name('admin.fresnsConsole.dashboard');
        Route::get('/settings', 'AmControllerWeb@settings')->name('admin.fresnsConsole.settings');
        Route::get('/keys', 'AmControllerWeb@keys')->name('admin.fresnsConsole.keys');
        Route::get('/admins', 'AmControllerWeb@admins')->name('admin.fresnsConsole.admins');
        Route::get('/websites', 'AmControllerWeb@websites')->name('admin.fresnsConsole.websites');
        Route::get('/apps', 'AmControllerWeb@apps')->name('admin.fresnsConsole.apps');
        Route::get('/plugins', 'AmControllerWeb@plugins')->name('admin.fresnsConsole.plugins');
        Route::get('/iframe', 'AmControllerWeb@iframe')->name('admin.fresnsConsole.iframe');
        // Logout Console
        Route::get('/logout', 'AmControllerWeb@logout')->name('admin.fresnsConsole.logout');
        // Setting Language
        Route::post('/setLanguage', 'AmControllerWeb@setLanguage')->name('admin.fresnsConsole.setLanguage');
        // Console Settings
        Route::post('/updateSetting', 'AmControllerWeb@updateSetting')->name('admin.fresnsConsole.updateSetting');
        // Administrator Settings
        Route::post('/addAdmin', 'AmControllerWeb@addAdmin')->name('admin.fresnsConsole.addAdmin');
        Route::post('/delAdmin', 'AmControllerWeb@delAdmin')->name('admin.fresnsConsole.delAdmin');
        // Key Management
        Route::post('/submitKey', 'AmControllerWeb@submitKey')->name('admin.fresnsConsole.submitKey');
        Route::post('/updateKey', 'AmControllerWeb@updateKey')->name('admin.fresnsConsole.updateKey');
        Route::post('/resetKey', 'AmControllerWeb@resetKey')->name('admin.fresnsConsole.resetKey');
        Route::post('/delKey', 'AmControllerWeb@delKey')->name('admin.fresnsConsole.delKey');
        // Extensions Related
        Route::post('/install', 'AmControllerWeb@install')->name('admin.fresnsConsole.install');
        Route::post('/uninstall', 'AmControllerWeb@uninstall')->name('admin.fresnsConsole.uninstall');
        Route::post('/updateUnikey', 'AmControllerWeb@updateUnikey')->name('admin.fresnsConsole.updateUnikey');
        Route::post('/localInstall', 'AmControllerWeb@localInstall')->name('admin.fresnsConsole.localInstall');
        Route::post('/enableUnikeyStatus', 'AmControllerWeb@enableUnikeyStatus')->name('admin.fresnsConsole.install');
        Route::post('/websiteLinkSubject', 'AmControllerWeb@websiteLinkSubject')->name('admin.fresnsConsole.websiteLinkSubject');
    });
}
