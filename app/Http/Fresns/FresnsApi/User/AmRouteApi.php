<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_

Route::group(['prefix' => 'fresns/user', 'namespace' => '\App\Http\Fresns\FresnsApi\User'], function () {
    //注册
    Route::post('/register', 'AmControllerApi@register')->name('admin.user.register');
    //退出登陆
    Route::post('/logout', 'AmControllerApi@logout')->name('admin.user.logout');
    //登陆
    Route::post('/login', 'AmControllerApi@login')->name('admin.user.login');
    //注销
    Route::post('/delete', 'AmControllerApi@userDelete')->name('admin.user.userDelete');
    //恢复
    Route::post('/restore', 'AmControllerApi@restore')->name('admin.user.userRestore');
    //重置密码
    Route::post('/reset', 'AmControllerApi@userReset')->name('admin.user.userReset');
    //用户基本信息
    Route::post('/detail', 'AmControllerApi@userInfo')->name('admin.user.userInfo');
    //钱包交易记录
    Route::post('/walletLogs', 'AmControllerApi@userWalletLogs')->name('admin.user.walletLogs');
    //修改用户资料
    Route::post('/edit', 'AmControllerApi@userEdit')->name('admin.user.userEdit');

});