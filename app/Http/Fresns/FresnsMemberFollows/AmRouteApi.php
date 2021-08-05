<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_members_follows member_follows
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsMemberFollows'],
    function () {
        Route::get('/fresnsmemberfollows/index', 'AmControllerAdmin@index')->name('admin.fresnsmemberfollows.index');
        Route::post('/fresnsmemberfollows/store', 'AmControllerAdmin@store')->name('admin.fresnsmemberfollows.store');
        Route::post('/fresnsmemberfollows/update',
            'AmControllerAdmin@update')->name('admin.fresnsmemberfollows.update');
        Route::post('/fresnsmemberfollows/destroy',
            'AmControllerAdmin@destroy')->name('admin.fresnsmemberfollows.destroy');
        Route::get('/fresnsmemberfollows/detail', 'AmControllerAdmin@detail')->name('admin.fresnsmemberfollows.detail');
        Route::get('/fresnsmemberfollows/export', 'AmControllerAdmin@export')->name('admin.fresnsmemberfollows.export');
    });
