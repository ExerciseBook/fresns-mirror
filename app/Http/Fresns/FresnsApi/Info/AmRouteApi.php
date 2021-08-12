<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_
Route::group(['prefix' => 'fresns/info', 'namespace' => '\App\Http\Fresns\FresnsApi\Info'], function () {
    //系统配置信息
    Route::post('/configs', 'AmControllerApi@infoConfigs')->name('admin.info.configs');
    //获取表情包列表
    Route::post('/emojis', 'AmControllerApi@infoEmojis')->name('admin.info.emojis');
    //敏感词
    Route::post('/stopWords', 'AmControllerApi@infoStopWords')->name('admin.info.stopWords');
    //上传交互日志
    Route::post('/uploadLog', 'AmControllerApi@infoUploadLog')->name('admin.info.uploadLog');
    //
    Route::post('/inputtips', 'AmControllerApi@infoInputtips')->name('admin.info.infoInputtips');

    // 扩展配置信息
    Route::post('/expands', 'AmControllerApi@expands')->name('admin.info.expands');
    //下载内容文件
    Route::post('/downloadFile', 'AmControllerApi@downloadFile')->name('admin.info.downloadFile');

    // 发送验证码
    Route::post('/sendVerifyCode', 'AmControllerApi@sendVerifyCode')->name('admin.info.sendVerifyCode');

    // 全局摘要信息
    Route::post('/summary', 'AmControllerApi@summary')->name('admin.info.summary');


});
