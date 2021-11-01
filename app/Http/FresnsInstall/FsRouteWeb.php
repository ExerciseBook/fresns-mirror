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
        Route::get('/step1', 'FsControllerWeb@settings')->name('install.step1');
        Route::get('/step2', 'FsControllerWeb@keys')->name('install.step2');
        Route::get('/step3', 'FsControllerWeb@admins')->name('install.step3');
        Route::get('/step4', 'FsControllerWeb@websites')->name('install.step4');
        Route::get('/finish', 'FsControllerWeb@apps')->name('install.finish');

        // operation request
        Route::post('/env', 'FsControllerWeb@env')->name('install.env');

    });
}
