<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns User API
Route::group(['prefix' => 'fresns/user', 'namespace' => '\App\Http\FresnsApi\User'], function () {
    Route::post('/register', 'FsControllerApi@register')->name('api.user.register');
    Route::post('/login', 'FsControllerApi@login')->name('api.user.login');
    Route::post('/logout', 'FsControllerApi@logout')->name('api.user.logout');
    Route::post('/delete', 'FsControllerApi@delete')->name('api.user.delete');
    Route::post('/restore', 'FsControllerApi@restore')->name('api.user.restore');
    Route::post('/reset', 'FsControllerApi@reset')->name('api.user.reset');
    Route::post('/detail', 'FsControllerApi@detail')->name('api.user.detail');
    Route::post('/edit', 'FsControllerApi@edit')->name('api.user.edit');
    Route::post('/walletLogs', 'FsControllerApi@walletLogs')->name('api.user.walletLogs');
});
