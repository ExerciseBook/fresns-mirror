<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns Editor API
Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\FresnsApi\Editor'], function () {
    // Pull
    Route::post('/editor/create', 'AmControllerApi@create')->name('admin.editor.create');
    Route::post('/editor/lists', 'AmControllerApi@lists')->name('admin.editor.lists');
    Route::post('/editor/detail', 'AmControllerApi@detail')->name('admin.editor.detail');
    // Push
    Route::post('/editor/update', 'AmControllerApi@update')->name('admin.editor.update');
    Route::post('/editor/submit', 'AmControllerApi@submit')->name('admin.editor.submit');
    Route::post('/editor/publish', 'AmControllerApi@publish')->name('admin.editor.publish');
    // Operation
    Route::post('/editor/upload', 'AmControllerApi@upload')->name('admin.editor.upload');
    Route::post('/editor/uploadToken', 'AmControllerApi@uploadToken')->name('admin.editor.uploadToken');
    Route::post('/editor/delete', 'AmControllerApi@delete')->name('admin.editor.delete');
    Route::post('/editor/revoke', 'AmControllerApi@revoke')->name('admin.editor.revoke');
});
