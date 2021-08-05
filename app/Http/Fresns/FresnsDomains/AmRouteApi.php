<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_domains domains
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsDomains'],
    function () {
        Route::get('/fresnsdomains/index', 'AmControllerAdmin@index')->name('admin.fresnsdomains.index');
        Route::post('/fresnsdomains/store', 'AmControllerAdmin@store')->name('admin.fresnsdomains.store');
        Route::post('/fresnsdomains/update', 'AmControllerAdmin@update')->name('admin.fresnsdomains.update');
        Route::post('/fresnsdomains/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsdomains.destroy');
        Route::get('/fresnsdomains/detail', 'AmControllerAdmin@detail')->name('admin.fresnsdomains.detail');
        Route::get('/fresnsdomains/export', 'AmControllerAdmin@export')->name('admin.fresnsdomains.export');
    });
