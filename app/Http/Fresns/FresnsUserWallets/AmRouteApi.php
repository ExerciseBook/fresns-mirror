<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_user_wallets user_wallets
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsUserWallets'],
    function () {
        Route::get('/fresnsuserwallets/index', 'AmControllerAdmin@index')->name('admin.fresnsuserwallets.index');
        Route::post('/fresnsuserwallets/store', 'AmControllerAdmin@store')->name('admin.fresnsuserwallets.store');
        Route::post('/fresnsuserwallets/update', 'AmControllerAdmin@update')->name('admin.fresnsuserwallets.update');
        Route::post('/fresnsuserwallets/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsuserwallets.destroy');
        Route::get('/fresnsuserwallets/detail', 'AmControllerAdmin@detail')->name('admin.fresnsuserwallets.detail');
        Route::get('/fresnsuserwallets/export', 'AmControllerAdmin@export')->name('admin.fresnsuserwallets.export');
    });
