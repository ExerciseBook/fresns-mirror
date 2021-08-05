<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_session_keys session_keys
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsSessionKeys'],
    function () {
        Route::get('/fresnssessionkeys/index', 'AmControllerAdmin@index')->name('admin.fresnssessionkeys.index');
        Route::post('/fresnssessionkeys/store', 'AmControllerAdmin@store')->name('admin.fresnssessionkeys.store');
        Route::post('/fresnssessionkeys/update', 'AmControllerAdmin@update')->name('admin.fresnssessionkeys.update');
        Route::post('/fresnssessionkeys/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnssessionkeys.destroy');
        Route::get('/fresnssessionkeys/detail', 'AmControllerAdmin@detail')->name('admin.fresnssessionkeys.detail');
        Route::get('/fresnssessionkeys/export', 'AmControllerAdmin@export')->name('admin.fresnssessionkeys.export');
    });
