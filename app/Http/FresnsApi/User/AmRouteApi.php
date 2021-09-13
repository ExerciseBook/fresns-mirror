<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns User API
Route::group(['prefix' => 'fresns/user', 'namespace' => '\App\Http\FresnsApi\User'], function () {
    Route::post('/register', 'AmControllerApi@register')->name('api.user.register');
    Route::post('/login', 'AmControllerApi@login')->name('api.user.login');
    Route::post('/logout', 'AmControllerApi@logout')->name('api.user.logout');
    Route::post('/delete', 'AmControllerApi@delete')->name('api.user.delete');
    Route::post('/restore', 'AmControllerApi@restore')->name('api.user.restore');
    Route::post('/reset', 'AmControllerApi@reset')->name('api.user.reset');
    Route::post('/detail', 'AmControllerApi@detail')->name('api.user.detail');
    Route::post('/edit', 'AmControllerApi@edit')->name('api.user.edit');
    Route::post('/walletLogs', 'AmControllerApi@walletLogs')->name('api.user.walletLogs');
});
