<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_mentions mentions
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsMentions'],
    function () {
        Route::get('/fresnsmentions/index', 'AmControllerAdmin@index')->name('admin.fresnsmentions.index');
        Route::post('/fresnsmentions/store', 'AmControllerAdmin@store')->name('admin.fresnsmentions.store');
        Route::post('/fresnsmentions/update', 'AmControllerAdmin@update')->name('admin.fresnsmentions.update');
        Route::post('/fresnsmentions/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsmentions.destroy');
        Route::get('/fresnsmentions/detail', 'AmControllerAdmin@detail')->name('admin.fresnsmentions.detail');
        Route::get('/fresnsmentions/export', 'AmControllerAdmin@export')->name('admin.fresnsmentions.export');
    });
