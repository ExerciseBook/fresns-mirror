<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_groups groups
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsGroups'],
    function () {
        Route::get('/fresnsgroups/index', 'AmControllerAdmin@index')->name('admin.fresnsgroups.index');
        Route::post('/fresnsgroups/store', 'AmControllerAdmin@store')->name('admin.fresnsgroups.store');
        Route::post('/fresnsgroups/update', 'AmControllerAdmin@update')->name('admin.fresnsgroups.update');
        Route::post('/fresnsgroups/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsgroups.destroy');
        Route::get('/fresnsgroups/detail', 'AmControllerAdmin@detail')->name('admin.fresnsgroups.detail');
        Route::get('/fresnsgroups/export', 'AmControllerAdmin@export')->name('admin.fresnsgroups.export');
        Route::get('/fresnsgroups/index2', 'AmControllerAdmin@index2')->name('admin.fresnsgroups.index2');
        Route::post('/fresnsgroups/moveByGroups', 'AmControllerAdmin@moveByGroups')->name('admin.fresnsgroups.moveByGroups');

    });
