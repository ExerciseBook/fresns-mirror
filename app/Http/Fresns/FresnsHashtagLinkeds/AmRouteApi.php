<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_hashtag_linkeds hashtag_linkeds
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsHashtagLinkeds'],
    function () {
        Route::get('/fresnshashtaglinkeds/index', 'AmControllerAdmin@index')->name('admin.fresnshashtaglinkeds.index');
        Route::post('/fresnshashtaglinkeds/store', 'AmControllerAdmin@store')->name('admin.fresnshashtaglinkeds.store');
        Route::post('/fresnshashtaglinkeds/update',
            'AmControllerAdmin@update')->name('admin.fresnshashtaglinkeds.update');
        Route::post('/fresnshashtaglinkeds/destroy',
            'AmControllerAdmin@destroy')->name('admin.fresnshashtaglinkeds.destroy');
        Route::get('/fresnshashtaglinkeds/detail',
            'AmControllerAdmin@detail')->name('admin.fresnshashtaglinkeds.detail');
        Route::get('/fresnshashtaglinkeds/export',
            'AmControllerAdmin@export')->name('admin.fresnshashtaglinkeds.export');
    });
