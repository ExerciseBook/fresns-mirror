<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_comments comments
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsComments'],
    function () {
        Route::get('/fresnscomments/index', 'AmControllerAdmin@index')->name('admin.fresnscomments.index');
        Route::post('/fresnscomments/store', 'AmControllerAdmin@store')->name('admin.fresnscomments.store');
        Route::post('/fresnscomments/update', 'AmControllerAdmin@update')->name('admin.fresnscomments.update');
        Route::post('/fresnscomments/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnscomments.destroy');
        Route::get('/fresnscomments/detail', 'AmControllerAdmin@detail')->name('admin.fresnscomments.detail');
        Route::get('/fresnscomments/export', 'AmControllerAdmin@export')->name('admin.fresnscomments.export');
    });
