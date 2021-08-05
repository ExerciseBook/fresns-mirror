<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_members_likes member_likes
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsMemberLikes'],
    function () {
        Route::get('/fresnsmemberlikes/index', 'AmControllerAdmin@index')->name('admin.fresnsmemberlikes.index');
        Route::post('/fresnsmemberlikes/store', 'AmControllerAdmin@store')->name('admin.fresnsmemberlikes.store');
        Route::post('/fresnsmemberlikes/update', 'AmControllerAdmin@update')->name('admin.fresnsmemberlikes.update');
        Route::post('/fresnsmemberlikes/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsmemberlikes.destroy');
        Route::get('/fresnsmemberlikes/detail', 'AmControllerAdmin@detail')->name('admin.fresnsmemberlikes.detail');
        Route::get('/fresnsmemberlikes/export', 'AmControllerAdmin@export')->name('admin.fresnsmemberlikes.export');
    });
