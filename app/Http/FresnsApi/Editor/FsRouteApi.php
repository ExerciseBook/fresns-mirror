<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns Editor API
Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\FresnsApi\Editor'], function () {
    // Pull
    Route::post('/editor/create', 'AmControllerApi@create')->name('api.editor.create');
    Route::post('/editor/lists', 'AmControllerApi@lists')->name('api.editor.lists');
    Route::post('/editor/detail', 'AmControllerApi@detail')->name('api.editor.detail');
    // Push
    Route::post('/editor/update', 'AmControllerApi@update')->name('api.editor.update');
    Route::post('/editor/submit', 'AmControllerApi@submit')->name('api.editor.submit');
    Route::post('/editor/publish', 'AmControllerApi@publish')->name('api.editor.publish');
    // Operation
    Route::post('/editor/upload', 'AmControllerApi@upload')->name('api.editor.upload');
    Route::post('/editor/uploadToken', 'AmControllerApi@uploadToken')->name('api.editor.uploadToken');
    Route::post('/editor/delete', 'AmControllerApi@delete')->name('api.editor.delete');
    Route::post('/editor/revoke', 'AmControllerApi@revoke')->name('api.editor.revoke');
});
