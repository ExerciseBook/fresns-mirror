<?php
/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 设置web路由 - db替换，没有的话为默认路由
Route::get('/', function () {
    return view('welcome');
});

// 查看日志
Route::get('log3', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('clearLog', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

// clearLog
Route::get('/clearLog3', '\App\Plugins\Share\Area\AmControllerApi@clearLog3')->name('admin.area.clearLog3');
