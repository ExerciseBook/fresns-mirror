<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_members_icons member_icons
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsMemberIcons'],
    function () {
        Route::get('/fresnsmembericons/index', 'AmControllerAdmin@index')->name('admin.fresnsmembericons.index');
        Route::post('/fresnsmembericons/store', 'AmControllerAdmin@store')->name('admin.fresnsmembericons.store');
        Route::post('/fresnsmembericons/update', 'AmControllerAdmin@update')->name('admin.fresnsmembericons.update');
        Route::post('/fresnsmembericons/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsmembericons.destroy');
        Route::get('/fresnsmembericons/detail', 'AmControllerAdmin@detail')->name('admin.fresnsmembericons.detail');
        Route::get('/fresnsmembericons/export', 'AmControllerAdmin@export')->name('admin.fresnsmembericons.export');
    });
