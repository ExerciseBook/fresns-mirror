<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns Member API
Route::group(['prefix' => 'fresns/member', 'namespace' => '\App\Http\FresnsApi\Member'], function () {
    // Member Login
    Route::post('/auth', 'AmControllerApi@auth')->name('admin.member.auth');
    // Member Edit Profile
    Route::post('/edit', 'AmControllerApi@memberEdit')->name('admin.member.edit');
    // Member Operation Mark
    Route::post('/mark', 'AmControllerApi@memberMark')->name('admin.member.memberMark');
    // Member Mark Data List
    Route::post('/markLists', 'AmControllerApi@memberMarkLists')->name('admin.member.memberMarkLists');
    // Get Member Role List
    Route::post('/roles', 'AmControllerApi@memberRoles')->name('admin.member.memberRoles');
    // Member Operation Delete
    Route::post('/delete', 'AmControllerApi@memberDelete')->name('admin.member.memberDelete');
    // Get Member Detail
    Route::post('/detail', 'AmControllerApi@memberDetail')->name('admin.member.memberDetail');
    // Get Member List
    Route::post('/lists', 'AmControllerApi@memberLists')->name('admin.member.memberLists');
    // Get Member Interactions Data
    Route::post('/interactions', 'AmControllerApi@memberInteractions')->name('admin.member.memberInteractions');
});
