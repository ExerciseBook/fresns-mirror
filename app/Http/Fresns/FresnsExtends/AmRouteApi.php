<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_extends extends
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsExtends'],
    function () {
        Route::get('/fresnsextends/index', 'AmControllerAdmin@index')->name('admin.fresnsextends.index');
        Route::post('/fresnsextends/store', 'AmControllerAdmin@store')->name('admin.fresnsextends.store');
        Route::post('/fresnsextends/update', 'AmControllerAdmin@update')->name('admin.fresnsextends.update');
        Route::post('/fresnsextends/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsextends.destroy');
        Route::get('/fresnsextends/detail', 'AmControllerAdmin@detail')->name('admin.fresnsextends.detail');
        Route::get('/fresnsextends/export', 'AmControllerAdmin@export')->name('admin.fresnsextends.export');
    });
