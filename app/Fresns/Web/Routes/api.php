<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Fresns\Web\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('engine')
    ->middleware([
        AccountAuthorize::class,
        UserAuthorize::class,
    ])
    ->group(function () {

        Route::post('send-verify-code', [ApiController::class, 'sendVerifyCode'])->name('send.verifyCode')->withoutMiddleware(['AccountAuthorize', 'UserAuthorize']);

        Route::post('register', [ApiController::class, 'accountRegister'])->name('register')->withoutMiddleware(['AccountAuthorize', 'UserAuthorize']);
        Route::post('login', [ApiController::class, 'accountLogin'])->name('login')->withoutMiddleware(['AccountAuthorize', 'UserAuthorize']);
        Route::post('reset-password', [ApiController::class, 'resetPassword'])->name('resetPassword')->withoutMiddleware(['AccountAuthorize', 'UserAuthorize']);
        Route::delete('logout', [AccountController::class, 'logout'])->name('logout')->withoutMiddleware(['UserAuthorize']);

        Route::post('auth', [AccountController::class, 'userAuth'])->name('user.auth')->withoutMiddleware(['UserAuthorize']);
        Route::post('mark', [ApiController::class, 'userMark'])->name('mark');
        Route::put('mark-note', [ApiController::class, 'userMarkNote'])->name('mark.note');

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