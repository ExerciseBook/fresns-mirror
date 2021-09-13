<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns Info API
Route::group(['prefix' => 'fresns/info', 'namespace' => '\App\Http\FresnsApi\Info'], function () {
    // System Config Info
    Route::post('/configs', 'AmControllerApi@infoConfigs')->name('admin.info.configs');
    // Extensions Config Info
    Route::post('/extensions', 'AmControllerApi@extensions')->name('admin.info.extensions');
    // Overview
    Route::post('/overview', 'AmControllerApi@overview')->name('admin.info.overview');
    // Emojis
    Route::post('/emojis', 'AmControllerApi@infoEmojis')->name('admin.info.emojis');
    // Stop Words
    Route::post('/stopWords', 'AmControllerApi@infoStopWords')->name('admin.info.stopWords');
    // Send Verify Code
    Route::post('/sendVerifyCode', 'AmControllerApi@sendVerifyCode')->name('admin.info.sendVerifyCode');
    // Input Tips
    Route::post('/inputtips', 'AmControllerApi@infoInputtips')->name('admin.info.infoInputtips');
    // Upload Log
    Route::post('/uploadLog', 'AmControllerApi@infoUploadLog')->name('admin.info.uploadLog');
    // Download File
    Route::post('/downloadFile', 'AmControllerApi@downloadFile')->name('admin.info.downloadFile');
});
