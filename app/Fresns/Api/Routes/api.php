<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Fresns\Api\Http\Controllers\AccountController;
use App\Fresns\Api\Http\Controllers\UserController;
use App\Fresns\Api\Http\Controllers\GroupController;
use App\Fresns\Api\Http\Controllers\HashtagController;
use App\Fresns\Api\Http\Controllers\PostController;
use App\Fresns\Api\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->group(function () {
    Route::prefix('account')->group(function () {
        Route::get('detail', [AccountController::class, 'detail'])->name('account.detail');
    });

    Route::prefix('user')->group(function () {
        Route::get('detail/{uidOrUsername}', [UserController::class, 'detail'])->name('user.detail');
    });

    Route::prefix('group')->group(function () {
        Route::get('detail/{gid}', [GroupController::class, 'detail'])->name('group.detail');
    });

    Route::prefix('hashtag')->group(function () {
        Route::get('detail/{hid}', [HashtagController::class, 'detail'])->name('hashtag.detail');
    });

    Route::prefix('post')->group(function () {
        Route::get('detail/{pid}', [PostController::class, 'detail'])->name('post.detail');
    });

    Route::prefix('comment')->group(function () {
        Route::get('detail/{cid}', [CommentController::class, 'detail'])->name('comment.detail');
    });
});
