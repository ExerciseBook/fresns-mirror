<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_
Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\Fresns\FresnsApi\Editor'], function () {
    // 创建新草稿
    Route::post('/editor/create', 'AmControllerApi@create')->name('admin.editor.create');
    // 获取草稿详情
    Route::post('/editor/detail', 'AmControllerApi@detail')->name('admin.editor.detail');
    // 获取列表
    Route::post('/editor/lists', 'AmControllerApi@lists')->name('admin.editor.lists');
    // 更新草稿内容
    Route::post('/editor/update', 'AmControllerApi@update')->name('admin.editor.update');
    // 提交内容
    Route::post('/editor/submit', 'AmControllerApi@submit')->name('admin.editor.submit');
    // 快速发表
    Route::post('/editor/publish', 'AmControllerApi@publish')->name('admin.editor.publish');
    // 撤回审核中草稿
    Route::post('/editor/revoke', 'AmControllerApi@revoke')->name('admin.editor.revoke');

    //上传文件
    Route::post('/editor/upload', 'AmControllerApi@upload')->name('admin.editor.upload');
    //删除草稿
    Route::post('/editor/delete', 'AmControllerApi@delete')->name('admin.editor.delete');
    Route::post('/editor/uploadToken', 'AmControllerApi@uploadToken')->name('admin.editor.uploadToken');
});
