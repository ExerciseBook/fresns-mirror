<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 插件路由

// 插件基础操作
Route::group(['prefix' => 'fresns', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Center\Scene'], function () {

    // 文件上传
    Route::post('/scene/uploadFile', 'FileSceneController@uploadFile')->name('fresns.scene.file.uploadFile');

    // 发送短信
    Route::post('/scene/send/sms', 'SmsSceneController@sendSms')->name('fresns.scene.sms.sendSms');

});

// 插件后台操作
Route::group(['prefix' => 'fresns', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Center\Market'], function () {

});
