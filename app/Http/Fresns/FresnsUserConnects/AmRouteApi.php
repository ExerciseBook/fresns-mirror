<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Tweet_user_connects user_connects
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsUserConnects'],
    function () {
        Route::get('/tweetuserconnects/index', 'AmControllerAdmin@index')->name('admin.tweetuserconnects.index');
        Route::post('/tweetuserconnects/store', 'AmControllerAdmin@store')->name('admin.tweetuserconnects.store');
        Route::post('/tweetuserconnects/update', 'AmControllerAdmin@update')->name('admin.tweetuserconnects.update');
        Route::post('/tweetuserconnects/destroy', 'AmControllerAdmin@destroy')->name('admin.tweetuserconnects.destroy');
        Route::get('/tweetuserconnects/detail', 'AmControllerAdmin@detail')->name('admin.tweetuserconnects.detail');
        Route::get('/tweetuserconnects/export', 'AmControllerAdmin@export')->name('admin.tweetuserconnects.export');
    });
