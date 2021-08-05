<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// // Tweet配置列表 config
// Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Plugins\Tweet\TweetConfigs'], function(){
//     Route::get('/tweetconfigs/index', 'AmControllerAdmin@index')->name('admin.tweetconfigs.index');
//     //更新排序
//     Route::post('/tweetconfigs/updateRankNum', 'AmControllerAdmin@updateRankNum')->name('admin.tweetconfigs.updateRankNum');
//     Route::post('/tweetconfigs/store', 'AmControllerAdmin@store')->name('admin.tweetconfigs.store');
//     Route::post('/tweetconfigs/update', 'AmControllerAdmin@update')->name('admin.tweetconfigs.update');
//     Route::post('/tweetconfigs/destroy', 'AmControllerAdmin@destroy')->name('admin.tweetconfigs.destroy');
//     Route::get('/tweetconfigs/detail', 'AmControllerAdmin@detail')->name('admin.tweetconfigs.detail');
//     Route::get('/tweetconfigs/export', 'AmControllerAdmin@export')->name('admin.tweetconfigs.export');
//     //语言设置
//     Route::post('/tweetconfigs/storeLang', 'AmControllerAdmin@storeLang')->name('admin.tweetconfigs.storeLang');
//     Route::post('/tweetconfigs/updateLang', 'AmControllerAdmin@updateLang')->name('admin.tweetconfigs.updateLang');
//     Route::post('/tweetconfigs/destroyLang', 'AmControllerAdmin@destroyLang')->name('admin.tweetconfigs.destroyLang');

//     //是否开启多语言
//     Route::post('/tweetconfigs/updateLanguageStatus', 'AmControllerAdmin@updateLanguageStatus')->name('admin.tweetconfigs.updateLanguageStatus');

//     //默认语言
//     Route::post('/tweetconfigs/updateDefaultLanguage', 'AmControllerAdmin@updateDefaultLanguage')->name('admin.tweetconfigs.updateDefaultLanguage');

//     //查看语言包
//     Route::post('/tweetconfigs/pack/index', 'AmControllerAdmin@packIndex')->name('admin.tweetconfigs.packIndex');
//     //新增语言包
//     Route::post('/tweetconfigs/pack/store', 'AmControllerAdmin@packStore')->name('admin.tweetconfigs.packStore');
//     //编辑
//     Route::post('/tweetconfigs/pack/update', 'AmControllerAdmin@packUpdate')->name('admin.tweetconfigs.packUpdate');
//     //删除
//     Route::post('/tweetconfigs/pack/destroy', 'AmControllerAdmin@packDestroy')->name('admin.tweetconfigs.packDestroy');

//     //地图设置
//     Route::get('/tweetconfigs/map/index', 'AmControllerAdmin@mapIndex')->name('admin.tweetconfigs.mapIndex');
//     Route::post('/tweetconfigs/map/store', 'AmControllerAdmin@mapStore')->name('admin.tweetconfigs.mapStore');
//     Route::post('/tweetconfigs/map/update', 'AmControllerAdmin@mapUpdate')->name('admin.tweetconfigs.mapUpdate');
//     Route::post('/tweetconfigs/map/destroy', 'AmControllerAdmin@destroy')->name('admin.tweetconfigs.destroy');


// });
