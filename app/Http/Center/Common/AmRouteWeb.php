<?php
/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Default Route
Route::get('/', function () {
    return view('commons.welcome');
});

// View Log
Route::get('log3', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('clearLog', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
