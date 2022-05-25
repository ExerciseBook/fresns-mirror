<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Fresns\Api\Http\Controllers\AccountController;
use App\Fresns\Api\Http\Controllers\CommentController;
use App\Fresns\Api\Http\Controllers\CommonController;
use App\Fresns\Api\Http\Controllers\GlobalController;
use App\Fresns\Api\Http\Controllers\GroupController;
use App\Fresns\Api\Http\Controllers\HashtagController;
use App\Fresns\Api\Http\Controllers\PostController;
use App\Fresns\Api\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    Route::prefix('global')->group(function () {
        Route::get('configs', [GlobalController::class, 'configs'])->name('global.configs');
        Route::get('token-for-upload', [GlobalController::class, 'tokenForUpload'])->name('common.tokenForUpload');
        Route::get('overview', [GlobalController::class, 'overview'])->name('global.overview');
        Route::get('roles', [GlobalController::class, 'roles'])->name('global.roles');
        Route::get('maps', [GlobalController::class, 'maps'])->name('global.maps');
        Route::get('content-type', [GlobalController::class, 'contentType'])->name('global.contentType');
        Route::get('stickers', [GlobalController::class, 'stickers'])->name('global.stickers');
        Route::get('block-words', [GlobalController::class, 'blockWords'])->name('global.blockWords');
    });

    Route::prefix('common')->group(function () {
        Route::get('input-tips', [CommonController::class, 'inputTips'])->name('common.inputTips');
        Route::get('callbacks', [CommonController::class, 'callbacks'])->name('common.callbacks');
        Route::get('download-file', [CommonController::class, 'downloadFile'])->name('common.downloadFile');
        Route::post('send-verify-code', [CommonController::class, 'sendVerifyCode'])->name('common.sendVerifyCode');
        Route::post('upload-log', [CommonController::class, 'uploadLog'])->name('common.uploadLog');
        Route::post('upload-file', [CommonController::class, 'uploadFile'])->name('common.uploadFile');
    });

    Route::prefix('account')->group(function () {
        Route::get('detail', [AccountController::class, 'detail'])->name('account.detail');
    });

    Route::prefix('user')->group(function () {
        Route::get('detail/{uidOrUsername}', [UserController::class, 'detail'])->name('user.detail');
    });

    Route::prefix('group')->group(function () {
        Route::get('list', [GroupController::class, 'list'])->name('group.list');
        Route::get('detail/{gid}', [GroupController::class, 'detail'])->name('group.detail');
    });

    Route::prefix('hashtag')->group(function () {
        Route::get('detail/{hid}', [HashtagController::class, 'detail'])->name('hashtag.detail');
    });

    Route::prefix('post')->group(function () {
        Route::get('list', [PostController::class, 'list'])->name('post.list');
        Route::get('detail/{pid}', [PostController::class, 'detail'])->name('post.detail');
    });

    Route::prefix('comment')->group(function () {
        Route::get('detail/{cid}', [CommentController::class, 'detail'])->name('comment.detail');
    });
});
