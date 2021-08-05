<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_notifies notifies
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsNotifies'],
    function () {
        Route::get('/fresnsnotifies/index', 'AmControllerAdmin@index')->name('admin.fresnsnotifies.index');
        Route::post('/fresnsnotifies/store', 'AmControllerAdmin@store')->name('admin.fresnsnotifies.store');
        Route::post('/fresnsnotifies/update', 'AmControllerAdmin@update')->name('admin.fresnsnotifies.update');
        Route::post('/fresnsnotifies/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsnotifies.destroy');
        Route::get('/fresnsnotifies/detail', 'AmControllerAdmin@detail')->name('admin.fresnsnotifies.detail');
        Route::get('/fresnsnotifies/export', 'AmControllerAdmin@export')->name('admin.fresnsnotifies.export');
    });
