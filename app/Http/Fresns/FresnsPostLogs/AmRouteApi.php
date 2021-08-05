<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_post_logs post_logs
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsPostLogs'],
    function () {
        Route::get('/fresnspostlogs/index', 'AmControllerAdmin@index')->name('admin.fresnspostlogs.index');
        Route::post('/fresnspostlogs/store', 'AmControllerAdmin@store')->name('admin.fresnspostlogs.store');
        Route::post('/fresnspostlogs/update', 'AmControllerAdmin@update')->name('admin.fresnspostlogs.update');
        Route::post('/fresnspostlogs/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnspostlogs.destroy');
        Route::get('/fresnspostlogs/detail', 'AmControllerAdmin@detail')->name('admin.fresnspostlogs.detail');
        Route::get('/fresnspostlogs/export', 'AmControllerAdmin@export')->name('admin.fresnspostlogs.export');
    });
