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


    Route::post('/testPlugin', 'AmControllerApi@testPlugin')->name('admin.info.testPlugin');
    //将configs内的数据更新
    Route::post('/updateConfigs', 'AmControllerApi@updateConfigs')->name('admin.info.updateConfigs');
    //创建交互凭证
    Route::post('/createSessionToken', 'AmControllerApi@createSessionToken')->name('admin.info.createSessionToken');
    //校验交互凭证
    Route::post('/checkSessionToken', 'AmControllerApi@checkSessionToken')->name('admin.info.checkSessionToken');
    //上传交互日志
    Route::post('/uploadSessionLog', 'AmControllerApi@uploadSessionLog')->name('admin.info.uploadSessionLog');
    //获取上传凭证
    Route::post('/getUploadToken', 'AmControllerApi@getUploadToken')->name('admin.info.getUploadToken');
    //上传文件
    Route::post('/uploadFile', 'AmControllerApi@uploadFile')->name('admin.info.uploadFile');
    //图片索要防盗链
    Route::post('/linkImage', 'AmControllerApi@linkImage')->name('admin.info.linkImage');
    //视频
    Route::post('/linkVideo', 'AmControllerApi@linkVideo')->name('admin.info.linkVideo');
    //音频
    Route::post('/linkAudio', 'AmControllerApi@linkAudio')->name('admin.info.linkAudio');
    //文档
    Route::post('/linkDoc', 'AmControllerApi@linkDoc')->name('admin.info.linkDoc');
    //fid命令字插件删除物理文件
    Route::post('/deleteFid', 'AmControllerApi@deleteFid')->name('admin.info.deleteFid');
    //钱包收入交易
    Route::post('/walletIncrease', 'AmControllerApi@walletIncrease')->name('admin.info.walletIncrease');
    //钱包支付交易
    Route::post('/walletDecrease', 'AmControllerApi@walletDecrease')->name('admin.info.walletDecrease');

});
