<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_session_tokens session_tokens
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsSessionTokens'],
    function () {
        Route::get('/fresnssessiontokens/index', 'AmControllerAdmin@index')->name('admin.fresnssessiontokens.index');
        Route::post('/fresnssessiontokens/store', 'AmControllerAdmin@store')->name('admin.fresnssessiontokens.store');
        Route::post('/fresnssessiontokens/update',
            'AmControllerAdmin@update')->name('admin.fresnssessiontokens.update');
        Route::post('/fresnssessiontokens/destroy',
            'AmControllerAdmin@destroy')->name('admin.fresnssessiontokens.destroy');
        Route::get('/fresnssessiontokens/detail', 'AmControllerAdmin@detail')->name('admin.fresnssessiontokens.detail');
        Route::get('/fresnssessiontokens/export', 'AmControllerAdmin@export')->name('admin.fresnssessiontokens.export');
    });
