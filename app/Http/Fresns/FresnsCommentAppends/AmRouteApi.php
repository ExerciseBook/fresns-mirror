<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_comment_appends comment_appends
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsCommentAppends'],
    function () {
        Route::get('/fresnscommentappends/index', 'AmControllerAdmin@index')->name('admin.fresnscommentappends.index');
        Route::post('/fresnscommentappends/store', 'AmControllerAdmin@store')->name('admin.fresnscommentappends.store');
        Route::post('/fresnscommentappends/update',
            'AmControllerAdmin@update')->name('admin.fresnscommentappends.update');
        Route::post('/fresnscommentappends/destroy',
            'AmControllerAdmin@destroy')->name('admin.fresnscommentappends.destroy');
        Route::get('/fresnscommentappends/detail',
            'AmControllerAdmin@detail')->name('admin.fresnscommentappends.detail');
        Route::get('/fresnscommentappends/export',
            'AmControllerAdmin@export')->name('admin.fresnscommentappends.export');
    });
