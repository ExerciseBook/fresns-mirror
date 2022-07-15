<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Fresns\Web\Http\Controllers\ApiController;
use App\Fresns\Web\Http\Middleware\AccountAuthorize;
use App\Fresns\Web\Http\Middleware\UserAuthorize;
use Illuminate\Support\Facades\Route;

Route::prefix('engine')
    ->middleware([
        AccountAuthorize::class,
        UserAuthorize::class,
    ])
    ->group(function () {

        Route::post('send-verify-code', [ApiController::class, 'sendVerifyCode'])->name('send.verifyCode')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
        Route::get('download-link', [ApiController::class, 'downloadLink'])->name('file.download');

        Route::post('register', [ApiController::class, 'accountRegister'])->name('register')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
        Route::post('login', [ApiController::class, 'accountLogin'])->name('login')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
        Route::post('reset-password', [ApiController::class, 'resetPassword'])->name('resetPassword')->withoutMiddleware([AccountAuthorize::class, UserAuthorize::class]);
        Route::delete('logout', [AccountController::class, 'logout'])->name('logout')->withoutMiddleware([UserAuthorize::class]);

        Route::prefix('user')->name('user.')->group(function () {
            Route::post('auth', [AccountController::class, 'userAuth'])->name('auth')->withoutMiddleware([UserAuthorize::class]);
            Route::post('mark', [ApiController::class, 'userMark'])->name('mark');
            Route::put('mark-note', [ApiController::class, 'userMarkNote'])->name('mark.note');
        });

        Route::delete('post/{pid}', [ApiController::class, 'postDelete'])->name('post.delete');
        Route::delete('comment/{pid}', [ApiController::class, 'commentDelete'])->name('comment.delete');

        Route::prefix('editor')->name('editor.')->group(function () {
            Route::get('{type}/drafts', [ApiController::class, 'drafts'])->name('drafts');
            Route::post('{type}/create', [ApiController::class, 'create'])->name('create');
            Route::get('{type}/{draftId}', [ApiController::class, 'detail'])->name('detail');
            Route::put('{type}/{draftId}', [ApiController::class, 'update'])->name('update');
            Route::post('{type}/{draftId}', [ApiController::class, 'publish'])->name('publish');
            Route::patch('{type}/{draftId}', [ApiController::class, 'revoke'])->name('revoke');
            Route::delete('{type}/{draftId}', [ApiController::class, 'delete'])->name('delete');
            Route::post('direct-publish', [ApiController::class, 'directPublish'])->name('direct.publish');
        });
    });
