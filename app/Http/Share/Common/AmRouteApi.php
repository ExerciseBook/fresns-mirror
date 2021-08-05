<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 免登录
Route::group(['prefix' => 'admin'], function(){
  
    Route::post('/adminLogin', '\App\Plugins\Tweet\TweetAdmin\LoginController@adminLogin')->name('admin.auth.adminLogin');
});

// 登录
Route::group(['prefix' => 'admin'], function(){
    Route::post('/adminLogout', '\App\Plugins\Tweet\TweetAdmin\LoginController@adminLogout')->name('admin.auth.adminLogout');
});

// 当前登录用户
Route::group(['middleware' => 'auth:api'], function(){
    Route::get('/currentUser', '\App\Plugins\Tweet\TweetAdmin\LoginController@currentUser')->name('admin.auth.currentUser');
});


