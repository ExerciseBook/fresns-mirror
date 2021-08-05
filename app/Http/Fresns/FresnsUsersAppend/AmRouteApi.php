<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_user_append users_append
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsUsersAppend'],
    function () {
        Route::get('/fresnsusersappend/index', 'AmControllerAdmin@index')->name('admin.fresnsusersappend.index');
        Route::post('/fresnsusersappend/store', 'AmControllerAdmin@store')->name('admin.fresnsusersappend.store');
        Route::post('/fresnsusersappend/update', 'AmControllerAdmin@update')->name('admin.fresnsusersappend.update');
        Route::post('/fresnsusersappend/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsusersappend.destroy');
        Route::get('/fresnsusersappend/detail', 'AmControllerAdmin@detail')->name('admin.fresnsusersappend.detail');
        Route::get('/fresnsusersappend/export', 'AmControllerAdmin@export')->name('admin.fresnsusersappend.export');
    });
