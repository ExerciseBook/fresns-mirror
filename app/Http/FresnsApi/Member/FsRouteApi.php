<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns Member API
Route::group(['prefix' => 'fresns/member', 'namespace' => '\App\Http\FresnsApi\Member'], function () {
    // Member Login
    Route::post('/auth', 'AmControllerApi@auth')->name('api.member.auth');
    Route::post('/detail', 'AmControllerApi@detail')->name('api.member.detail');
    Route::post('/lists', 'AmControllerApi@lists')->name('api.member.lists');
    Route::post('/edit', 'AmControllerApi@edit')->name('api.member.edit');
    // Member Mark Operation
    Route::post('/mark', 'AmControllerApi@mark')->name('api.member.mark');
    Route::post('/markLists', 'AmControllerApi@markLists')->name('api.member.markLists');
    // Delete Post or Comment
    Route::post('/delete', 'AmControllerApi@delete')->name('api.member.delete');
    // Member Data
    Route::post('/roles', 'AmControllerApi@roles')->name('api.member.roles');
    Route::post('/interactions', 'AmControllerApi@interactions')->name('api.member.interactions');
});
