<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

//Fresns API
Route::group(['prefix' => '/fresns', 'namespace' => '\App\Http\FresnsApi\Notify'], function () {
    // 获取消息列表
    Route::post('/notify/lists', 'AmControllerApi@lists')->name('tweet.AmControllerApi.lists');
    // 更新阅读状态
    Route::post('/notify/read', 'AmControllerApi@read')->name('tweet.AmControllerApi.read');
    // 删除消息
    Route::post('/notify/delete', 'AmControllerApi@delete')->name('tweet.AmControllerApi.delete');
    // 获取会话列表
    Route::post('/dialog/lists', 'AmControllerApi@dialog_lists')->name('tweet.AmControllerApi.dialog_lists');
    // 获取消息列表
    Route::post('/dialog/messages', 'AmControllerApi@message_lists')->name('tweet.AmControllerApi.message_lists');
    // 更新阅读状态
    Route::post('/dialog/read', 'AmControllerApi@message_read')->name('tweet.AmControllerApi.message_read');
    // 发送消息
    Route::post('/dialog/send', 'AmControllerApi@message_send')->name('tweet.AmControllerApi.message_send');
    // 删除消息(会话)
    Route::post('/dialog/delete', 'AmControllerApi@dialog_delete')->name('tweet.AmControllerApi.dialog_delete');
});
