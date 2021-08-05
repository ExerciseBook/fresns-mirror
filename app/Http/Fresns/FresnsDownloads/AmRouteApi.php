<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_downloads downloads
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsDownloads'],
    function () {
        Route::get('/fresnsdownloads/index', 'AmControllerAdmin@index')->name('admin.fresnsdownloads.index');
        Route::post('/fresnsdownloads/store', 'AmControllerAdmin@store')->name('admin.fresnsdownloads.store');
        Route::post('/fresnsdownloads/update', 'AmControllerAdmin@update')->name('admin.fresnsdownloads.update');
        Route::post('/fresnsdownloads/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsdownloads.destroy');
        Route::get('/fresnsdownloads/detail', 'AmControllerAdmin@detail')->name('admin.fresnsdownloads.detail');
        Route::get('/fresnsdownloads/export', 'AmControllerAdmin@export')->name('admin.fresnsdownloads.export');
    });
