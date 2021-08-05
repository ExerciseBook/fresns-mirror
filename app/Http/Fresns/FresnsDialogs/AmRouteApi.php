<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Frensn_dialogs dialogs
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsDialogs'],
    function () {
        Route::get('/fresnsdialogs/index', 'AmControllerAdmin@index')->name('admin.fresnsdialogs.index');
        Route::post('/fresnsdialogs/store', 'AmControllerAdmin@store')->name('admin.fresnsdialogs.store');
        Route::post('/fresnsdialogs/update', 'AmControllerAdmin@update')->name('admin.fresnsdialogs.update');
        Route::post('/fresnsdialogs/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsdialogs.destroy');
        Route::get('/fresnsdialogs/detail', 'AmControllerAdmin@detail')->name('admin.fresnsdialogs.detail');
        Route::get('/fresnsdialogs/export', 'AmControllerAdmin@export')->name('admin.fresnsdialogs.export');
    });
