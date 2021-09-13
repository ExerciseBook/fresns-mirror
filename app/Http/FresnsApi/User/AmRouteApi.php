<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns User API
Route::group(['prefix' => 'fresns/user', 'namespace' => '\App\Http\FresnsApi\User'], function () {
    Route::post('/register', 'AmControllerApi@register')->name('admin.user.register');
    Route::post('/login', 'AmControllerApi@login')->name('admin.user.login');
    Route::post('/logout', 'AmControllerApi@logout')->name('admin.user.logout');
    Route::post('/delete', 'AmControllerApi@userDelete')->name('admin.user.userDelete');
    Route::post('/restore', 'AmControllerApi@restore')->name('admin.user.userRestore');
    Route::post('/reset', 'AmControllerApi@userReset')->name('admin.user.userReset');
    Route::post('/detail', 'AmControllerApi@userInfo')->name('admin.user.userInfo');
    Route::post('/edit', 'AmControllerApi@userEdit')->name('admin.user.userEdit');
    Route::post('/walletLogs', 'AmControllerApi@userWalletLogs')->name('admin.user.walletLogs');
});
