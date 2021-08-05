<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_posts posts
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsPosts'],
    function () {
        Route::get('/fresnsposts/index', 'AmControllerAdmin@index')->name('admin.fresnsposts.index');
        Route::post('/fresnsposts/store', 'AmControllerAdmin@store')->name('admin.fresnsposts.store');
        Route::post('/fresnsposts/update', 'AmControllerAdmin@update')->name('admin.fresnsposts.update');
        Route::post('/fresnsposts/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsposts.destroy');
        Route::get('/fresnsposts/detail', 'AmControllerAdmin@detail')->name('admin.fresnsposts.detail');
        Route::get('/fresnsposts/export', 'AmControllerAdmin@export')->name('admin.fresnsposts.export');
    });
