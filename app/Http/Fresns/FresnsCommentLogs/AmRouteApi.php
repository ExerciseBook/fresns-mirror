<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_comment_logs comment_logs
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsCommentLogs'],
    function () {
        Route::get('/fresnscommentlogs/index', 'AmControllerAdmin@index')->name('admin.fresnscommentlogs.index');
        Route::post('/fresnscommentlogs/store', 'AmControllerAdmin@store')->name('admin.fresnscommentlogs.store');
        Route::post('/fresnscommentlogs/update', 'AmControllerAdmin@update')->name('admin.fresnscommentlogs.update');
        Route::post('/fresnscommentlogs/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnscommentlogs.destroy');
        Route::get('/fresnscommentlogs/detail', 'AmControllerAdmin@detail')->name('admin.fresnscommentlogs.detail');
        Route::get('/fresnscommentlogs/export', 'AmControllerAdmin@export')->name('admin.fresnscommentlogs.export');
    });
