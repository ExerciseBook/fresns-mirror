<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_users users
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsUsers'],
    function () {
        Route::get('/fresnsusers/index', 'AmControllerAdmin@index')->name('admin.fresnsusers.index');
        Route::post('/fresnsusers/store', 'AmControllerAdmin@store')->name('admin.fresnsusers.store');
        Route::post('/fresnsusers/update', 'AmControllerAdmin@update')->name('admin.fresnsusers.update');
        Route::post('/fresnsusers/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsusers.destroy');
        Route::get('/fresnsusers/detail', 'AmControllerAdmin@detail')->name('admin.fresnsusers.detail');
        Route::get('/fresnsusers/export', 'AmControllerAdmin@export')->name('admin.fresnsusers.export');
    });
