<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_
Route::group(['prefix' => 'fresns/member', 'namespace' => '\App\Http\Fresns\FresnsApi\Member'], function () {
    //登陆
    Route::post('/auth', 'AmControllerApi@auth')->name('admin.member.auth');
    //修改资料
    Route::post('/edit', 'AmControllerApi@memberEdit')->name('admin.member.edit');
    // 成员操作标记内容
    Route::post('/mark', 'AmControllerApi@memberMark')->name('admin.member.memberMark');
    //成员操作删除内容
    Route::post('/delete', 'AmControllerApi@memberDelete')->name('admin.member.memberDelete');
    //获取成员
    Route::post('/detail', 'AmControllerApi@memberDetail')->name('admin.member.memberDetail');
    //获取成员列表
    Route::post('/lists', 'AmControllerApi@memberLists')->name('admin.member.memberLists');
    Route::post('/markLists', 'AmControllerApi@memberMarkLists')->name('admin.member.memberMarkLists');
    //获取成员【互动列表】
    Route::post('/interactions', 'AmControllerApi@memberInteractions')->name('admin.member.memberInteractions');

});
