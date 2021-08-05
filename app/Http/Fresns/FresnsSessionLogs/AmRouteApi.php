<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_session_logs session_logs
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsSessionLogs'],
    function () {
        Route::get('/fresnssessionlogs/index', 'AmControllerAdmin@index')->name('admin.fresnssessionlogs.index');
        Route::post('/fresnssessionlogs/store', 'AmControllerAdmin@store')->name('admin.fresnssessionlogs.store');
        Route::post('/fresnssessionlogs/update', 'AmControllerAdmin@update')->name('admin.fresnssessionlogs.update');
        Route::post('/fresnssessionlogs/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnssessionlogs.destroy');
        Route::get('/fresnssessionlogs/detail', 'AmControllerAdmin@detail')->name('admin.fresnssessionlogs.detail');
        Route::get('/fresnssessionlogs/export', 'AmControllerAdmin@export')->name('admin.fresnssessionlogs.export');
    });
