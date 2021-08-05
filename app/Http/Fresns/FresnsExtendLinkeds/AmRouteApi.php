<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_extend_linkeds extend_linkeds
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsExtendLinkeds'],
    function () {
        Route::get('/fresnsextendlinkeds/index', 'AmControllerAdmin@index')->name('admin.fresnsextendlinkeds.index');
        Route::post('/fresnsextendlinkeds/store', 'AmControllerAdmin@store')->name('admin.fresnsextendlinkeds.store');
        Route::post('/fresnsextendlinkeds/update',
            'AmControllerAdmin@update')->name('admin.fresnsextendlinkeds.update');
        Route::post('/fresnsextendlinkeds/destroy',
            'AmControllerAdmin@destroy')->name('admin.fresnsextendlinkeds.destroy');
        Route::get('/fresnsextendlinkeds/detail', 'AmControllerAdmin@detail')->name('admin.fresnsextendlinkeds.detail');
        Route::get('/fresnsextendlinkeds/export', 'AmControllerAdmin@export')->name('admin.fresnsextendlinkeds.export');
    });
