<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */


$appName = env('APP_NAME');
if ($appName == 'Fresns') {
    Route::group(['prefix' => 'install', 'namespace' => '\App\Http\FresnsInstall'], function () {
        // step page
        Route::get('/index', 'FsControllerWeb@index')->name('install.index');
        Route::get('/step1', 'FsControllerWeb@step1')->name('install.step1');
        Route::get('/step2', 'FsControllerWeb@step2')->name('install.step2');
        Route::get('/step3', 'FsControllerWeb@step3')->name('install.step3');
        Route::get('/step4', 'FsControllerWeb@step4')->name('install.step4');
        Route::get('/step5', 'FsControllerWeb@step5')->name('install.step5');

        // operation request
        Route::post('/env', 'FsControllerWeb@env')->name('install.env');
        Route::post('/mysql', 'FsControllerWeb@initMysql')->name('install.mysql');
        Route::post('/manage', 'FsControllerWeb@initManage')->name('install.manage');
    });
}
