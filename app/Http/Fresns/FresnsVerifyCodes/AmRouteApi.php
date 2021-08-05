<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_verify_codes verify_codes
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsVerifyCodes'],
    function () {
        Route::get('/fresnsverifycodes/index', 'AmControllerAdmin@index')->name('admin.fresnsverifycodes.index');
        Route::post('/fresnsverifycodes/store', 'AmControllerAdmin@store')->name('admin.fresnsverifycodes.store');
        Route::post('/fresnsverifycodes/update', 'AmControllerAdmin@update')->name('admin.fresnsverifycodes.update');
        Route::post('/fresnsverifycodes/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsverifycodes.destroy');
        Route::get('/fresnsverifycodes/detail', 'AmControllerAdmin@detail')->name('admin.fresnsverifycodes.detail');
        Route::get('/fresnsverifycodes/export', 'AmControllerAdmin@export')->name('admin.fresnsverifycodes.export');
    });
