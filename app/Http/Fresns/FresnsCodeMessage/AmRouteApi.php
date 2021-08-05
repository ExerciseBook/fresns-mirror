<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_users users
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsCodeMessage'],
    function () {
        Route::get('/fresnscodemessage/index', 'AmControllerAdmin@index')->name('admin.fresnsusers.index');
        Route::post('/fresnscodemessage/store', 'AmControllerAdmin@store')->name('admin.fresnsusers.store');
        Route::post('/fresnscodemessage/update', 'AmControllerAdmin@update')->name('admin.fresnsusers.update');
        Route::post('/fresnscodemessage/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsusers.destroy');
        Route::get('/fresnscodemessage/detail', 'AmControllerAdmin@detail')->name('admin.fresnsusers.detail');
        Route::get('/fresnscodemessage/export', 'AmControllerAdmin@export')->name('admin.fresnsusers.export');
    });
