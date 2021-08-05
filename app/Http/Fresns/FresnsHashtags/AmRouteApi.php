<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_hashtags hashtags
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsHashtags'],
    function () {
        Route::get('/fresnshashtags/index', 'AmControllerAdmin@index')->name('admin.fresnshashtags.index');
        Route::post('/fresnshashtags/store', 'AmControllerAdmin@store')->name('admin.fresnshashtags.store');
        Route::post('/fresnshashtags/update', 'AmControllerAdmin@update')->name('admin.fresnshashtags.update');
        Route::post('/fresnshashtags/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnshashtags.destroy');
        Route::get('/fresnshashtags/detail', 'AmControllerAdmin@detail')->name('admin.fresnshashtags.detail');
        Route::get('/fresnshashtags/export', 'AmControllerAdmin@export')->name('admin.fresnshashtags.export');
    });
