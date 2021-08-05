<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_members_stats member_stats
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsMemberStats'],
    function () {
        Route::get('/fresnsmemberstats/index', 'AmControllerAdmin@index')->name('admin.fresnsmemberstats.index');
        Route::post('/fresnsmemberstats/store', 'AmControllerAdmin@store')->name('admin.fresnsmemberstats.store');
        Route::post('/fresnsmemberstats/update', 'AmControllerAdmin@update')->name('admin.fresnsmemberstats.update');
        Route::post('/fresnsmemberstats/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsmemberstats.destroy');
        Route::get('/fresnsmemberstats/detail', 'AmControllerAdmin@detail')->name('admin.fresnsmemberstats.detail');
        Route::get('/fresnsmemberstats/export', 'AmControllerAdmin@export')->name('admin.fresnsmemberstats.export');
    });
