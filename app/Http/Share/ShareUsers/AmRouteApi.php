<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 用户管理 users
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Plugins\Share\ShareUsers'], function(){
    Route::get('/shareusers/index', 'AmControllerAdmin@index')->name('admin.shareusers.index');
    Route::post('/shareusers/store', 'AmControllerAdmin@store')->name('admin.shareusers.store');
    Route::post('/shareusers/update', 'AmControllerAdmin@update')->name('admin.shareusers.update');
    Route::post('/shareusers/destroy', 'AmControllerAdmin@destroy')->name('admin.shareusers.destroy');
    Route::get('/shareusers/detail', 'AmControllerAdmin@detail')->name('admin.shareusers.detail');

    

});

