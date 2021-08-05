<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_post_appends post_appends
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsPostAppends'],
    function () {
        Route::get('/fresnspostappends/index', 'AmControllerAdmin@index')->name('admin.fresnspostappends.index');
        Route::post('/fresnspostappends/store', 'AmControllerAdmin@store')->name('admin.fresnspostappends.store');
        Route::post('/fresnspostappends/update', 'AmControllerAdmin@update')->name('admin.fresnspostappends.update');
        Route::post('/fresnspostappends/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnspostappends.destroy');
        Route::get('/fresnspostappends/detail', 'AmControllerAdmin@detail')->name('admin.fresnspostappends.detail');
        Route::get('/fresnspostappends/export', 'AmControllerAdmin@export')->name('admin.fresnspostappends.export');
    });
