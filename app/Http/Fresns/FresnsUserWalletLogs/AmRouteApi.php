<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_user_wallet_logs user_wallet_logs
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsUserWalletLogs'],
    function () {
        Route::get('/fresnsuserwalletlogs/index', 'AmControllerAdmin@index')->name('admin.fresnsuserwalletlogs.index');
        Route::post('/fresnsuserwalletlogs/store', 'AmControllerAdmin@store')->name('admin.fresnsuserwalletlogs.store');
        Route::post('/fresnsuserwalletlogs/update',
            'AmControllerAdmin@update')->name('admin.fresnsuserwalletlogs.update');
        Route::post('/fresnsuserwalletlogs/destroy',
            'AmControllerAdmin@destroy')->name('admin.fresnsuserwalletlogs.destroy');
        Route::get('/fresnsuserwalletlogs/detail',
            'AmControllerAdmin@detail')->name('admin.fresnsuserwalletlogs.detail');
        Route::get('/fresnsuserwalletlogs/export',
            'AmControllerAdmin@export')->name('admin.fresnsuserwalletlogs.export');
    });
