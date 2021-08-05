<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_
Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\Fresns\FresnsApi\Content'], function () {
    Route::post('/group/lists', 'AmControllerApi@group_lists')->name('admin.content.group_lists');
    // 获取小组[树结构列表]
    Route::post('/group/trees', 'AmControllerApi@trees')->name('admin.content.trees');
    // 获取小组单条
    Route::post('/group/detail', 'AmControllerApi@group_detail')->name('admin.content.group_detail');
    // 获取帖子[列表]
    Route::post('/post/lists', 'AmControllerApi@post_lists')->name('admin.content.post_lists');
    // 获取帖子[单条]
    Route::post('/post/detail', 'AmControllerApi@post_detail')->name('admin.content.post_detail');
    // 获取话题列表
    Route::post('/hashtag/lists', 'AmControllerApi@hashtag_lists')->name('admin.content.hashtag_lists');
    // 获取话题单个
    Route::post('/hashtag/detail', 'AmControllerApi@hashtag_detail')->name('admin.content.hashtag_detail');
    // 获取评论[列表]
    Route::post('/comment/lists', 'AmControllerApi@comment_lists')->name('admin.content.comment_lists');
    // 获取评论【单条】
    Route::post('/comment/detail', 'AmControllerApi@commentDetail')->name('admin.content.commentDetail');
    // 获取帖子关注的[列表]
    Route::post('/post/follows', 'AmControllerApi@postFollows')->name('admin.content.postFollows');
    // 获取帖子附近的[列表]
    Route::post('/post/nearbys', 'AmControllerApi@postNearbys')->name('admin.content.postNearbys');
    // 获取扩展内容
    Route::post('/extend/lists', 'AmControllerApi@extendsLists')->name('admin.content.extendsLists');

    //下载内容附近【单个】
    Route::post('/content/download', 'AmControllerApi@contentDownload')->name('admin.content.contentDownload');

    // 内容互动记录[列表]
    Route::post('/content/members', 'AmControllerApi@content_members')->name('admin.content.content_members');
    Route::post('/group/testCmd', 'AmControllerApi@testCmd')->name('admin.content.testCmd');
    Route::post('/group/checkVerify', 'AmControllerApi@checkVerify')->name('admin.content.checkVerify');
    Route::post('/group/cmdSubmit', 'AmControllerApi@cmdSubmit')->name('admin.content.cmdSubmit');
    Route::post('/group/sendEmail', 'AmControllerApi@sendEmail')->name('admin.content.sendEmail');
    Route::post('/group/sendPhone', 'AmControllerApi@sendPhone')->name('admin.content.sendPhone');
    Route::post('/group/sendSmsPlg', 'AmControllerApi@sendSmsPlg')->name('admin.content.sendSmsPlg');
    Route::post('/group/checkSms', 'AmControllerApi@checkSms')->name('admin.content.checkSms');
    Route::post('/group/pushIos', 'AmControllerApi@pushIos')->name('admin.content.pushIos');
    Route::post('/group/pushAndroid', 'AmControllerApi@pushAndroid')->name('admin.content.pushAndroid');
    Route::post('/group/sendWechat', 'AmControllerApi@sendWechat')->name('admin.content.sendWechat');
    Route::post('/group/submitDrast', 'AmControllerApi@submitDrast')->name('admin.content.submitDrast');
});
