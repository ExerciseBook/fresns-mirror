<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_file_logs file_logs
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsFileLogs'],
    function () {
        Route::get('/fresnsfilelogs/index', 'AmControllerAdmin@index')->name('admin.fresnsfilelogs.index');
        Route::post('/fresnsfilelogs/store', 'AmControllerAdmin@store')->name('admin.fresnsfilelogs.store');
        Route::post('/fresnsfilelogs/update', 'AmControllerAdmin@update')->name('admin.fresnsfilelogs.update');
        Route::post('/fresnsfilelogs/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsfilelogs.destroy');
        Route::get('/fresnsfilelogs/detail', 'AmControllerAdmin@detail')->name('admin.fresnsfilelogs.detail');
        Route::get('/fresnsfilelogs/export', 'AmControllerAdmin@export')->name('admin.fresnsfilelogs.export');
    });
