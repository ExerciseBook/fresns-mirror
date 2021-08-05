<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_post_members post_members
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsPostMembers'],
    function () {
        Route::get('/fresnspostmembers/index', 'AmControllerAdmin@index')->name('admin.fresnspostmembers.index');
        Route::post('/fresnspostmembers/store', 'AmControllerAdmin@store')->name('admin.fresnspostmembers.store');
        Route::post('/fresnspostmembers/update', 'AmControllerAdmin@update')->name('admin.fresnspostmembers.update');
        Route::post('/fresnspostmembers/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnspostmembers.destroy');
        Route::get('/fresnspostmembers/detail', 'AmControllerAdmin@detail')->name('admin.fresnspostmembers.detail');
        Route::get('/fresnspostmembers/export', 'AmControllerAdmin@export')->name('admin.fresnspostmembers.export');
    });
