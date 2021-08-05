<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_post_allows post_allows
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsPostAllows'],
    function () {
        Route::get('/fresnspostallows/index', 'AmControllerAdmin@index')->name('admin.fresnspostallows.index');
        Route::post('/fresnspostallows/store', 'AmControllerAdmin@store')->name('admin.fresnspostallows.store');
        Route::post('/fresnspostallows/update', 'AmControllerAdmin@update')->name('admin.fresnspostallows.update');
        Route::post('/fresnspostallows/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnspostallows.destroy');
        Route::get('/fresnspostallows/detail', 'AmControllerAdmin@detail')->name('admin.fresnspostallows.detail');
        Route::get('/fresnspostallows/export', 'AmControllerAdmin@export')->name('admin.fresnspostallows.export');
    });
