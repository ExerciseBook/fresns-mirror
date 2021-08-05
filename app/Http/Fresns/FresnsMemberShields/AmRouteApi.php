<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_members_shieIds member_shields
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsMemberShields'],
    function () {
        Route::get('/fresnsmembershields/index', 'AmControllerAdmin@index')->name('admin.fresnsmembershields.index');
        Route::post('/fresnsmembershields/store', 'AmControllerAdmin@store')->name('admin.fresnsmembershields.store');
        Route::post('/fresnsmembershields/update',
            'AmControllerAdmin@update')->name('admin.fresnsmembershields.update');
        Route::post('/fresnsmembershields/destroy',
            'AmControllerAdmin@destroy')->name('admin.fresnsmembershields.destroy');
        Route::get('/fresnsmembershields/detail', 'AmControllerAdmin@detail')->name('admin.fresnsmembershields.detail');
        Route::get('/fresnsmembershields/export', 'AmControllerAdmin@export')->name('admin.fresnsmembershields.export');
    });
