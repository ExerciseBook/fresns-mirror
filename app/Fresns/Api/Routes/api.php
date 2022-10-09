<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

use App\Fresns\Api\Http\Controllers\AccountController;
use App\Fresns\Api\Http\Controllers\CommentController;
use App\Fresns\Api\Http\Controllers\CommonController;
use App\Fresns\Api\Http\Controllers\DialogController;
use App\Fresns\Api\Http\Controllers\EditorController;
use App\Fresns\Api\Http\Controllers\GlobalController;
use App\Fresns\Api\Http\Controllers\GroupController;
use App\Fresns\Api\Http\Controllers\HashtagController;
use App\Fresns\Api\Http\Controllers\NotifyController;
use App\Fresns\Api\Http\Controllers\PostController;
use App\Fresns\Api\Http\Controllers\SearchController;
use App\Fresns\Api\Http\Controllers\UserController;
use App\Fresns\Api\Http\Middleware\CheckHeader;
use App\Fresns\Subscribe\Middleware\UserActivate;
use Illuminate\Support\Facades\Route;

Route::prefix('v2')->middleware([
    CheckHeader::class,
    UserActivate::class,
])->group(function () {
    // global
    Route::prefix('global')->name('global.')->group(function () {
        Route::get('configs', [GlobalController::class, 'configs'])->name('configs');
        Route::get('{type}/archives', [GlobalController::class, 'archives'])->name('archives');
        Route::get('upload-token', [GlobalController::class, 'uploadToken'])->name('upload.token');
        Route::get('roles', [GlobalController::class, 'roles'])->name('roles');
        Route::get('maps', [GlobalController::class, 'maps'])->name('maps');
        Route::get('content-type', [GlobalController::class, 'contentType'])->name('content.type');
        Route::get('stickers', [GlobalController::class, 'stickers'])->name('stickers');
        Route::get('block-words', [GlobalController::class, 'blockWords'])->name('block.words');
    });

    // common
    Route::prefix('common')->name('common.')->group(function () {
        Route::get('input-tips', [CommonController::class, 'inputTips'])->name('input.tips');
        Route::get('callbacks', [CommonController::class, 'callbacks'])->name('callbacks');
        Route::post('send-verify-code', [CommonController::class, 'sendVerifyCode'])->name('send.verifyCode');
        Route::post('upload-log', [CommonController::class, 'uploadLog'])->name('upload.log');
        Route::post('upload-file', [CommonController::class, 'uploadFile'])->name('upload.file');
        Route::get('file/{fid}/download-link', [CommonController::class, 'downloadFile'])->name('download.file');
        Route::get('file/{fid}/users', [CommonController::class, 'downloadUsers'])->name('download.users');
    });

    // search
    Route::prefix('search')->name('search.')->group(function () {
        Route::get('users', [SearchController::class, 'users'])->name('users');
        Route::get('groups', [SearchController::class, 'groups'])->name('groups');
        Route::get('hashtags', [SearchController::class, 'hashtags'])->name('hashtags');
        Route::get('posts', [SearchController::class, 'posts'])->name('posts');
        Route::get('comments', [SearchController::class, 'comments'])->name('comments');
    });

    // account
    Route::prefix('account')->name('account.')->group(function () {
        Route::post('register', [AccountController::class, 'register'])->name('register');
        Route::post('login', [AccountController::class, 'login'])->name('login');
        Route::put('reset-password', [AccountController::class, 'resetPassword'])->name('reset.password');
        Route::get('detail', [AccountController::class, 'detail'])->name('detail');
        Route::get('wallet-logs', [AccountController::class, 'walletLogs'])->name('wallet.logs');
        Route::post('verify-identity', [AccountController::class, 'verifyIdentity'])->name('verify.identity');
        Route::put('edit', [AccountController::class, 'edit'])->name('edit');
        Route::delete('logout', [AccountController::class, 'logout'])->name('logout');
        Route::post('apply-delete', [AccountController::class, 'applyDelete'])->name('apply.delete');
        Route::post('revoke-delete', [AccountController::class, 'revokeDelete'])->name('revoke.delete');
    });

    // user
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('list', [UserController::class, 'list'])->name('list');
        Route::get('{uidOrUsername}/detail', [UserController::class, 'detail'])->name('detail');
        Route::get('{uidOrUsername}/interactive/{type}', [UserController::class, 'interactive'])->name('interactive');
        Route::get('{uidOrUsername}/mark/{markType}/{listType}', [UserController::class, 'markList'])->name('mark.list');
        Route::post('auth', [UserController::class, 'auth'])->name('auth');
        Route::get('panel', [UserController::class, 'panel'])->name('panel');
        Route::put('edit', [UserController::class, 'edit'])->name('edit');
        Route::post('mark', [UserController::class, 'mark'])->name('mark');
        Route::put('mark-note', [UserController::class, 'markNote'])->name('mark.note');
    });

    // notify
    Route::prefix('notify')->name('notify.')->group(function () {
        Route::get('{type}/list', [NotifyController::class, 'list'])->name('list');
        Route::put('mark-as-read', [NotifyController::class, 'markAsRead'])->name('read');
        Route::delete('delete', [NotifyController::class, 'delete'])->name('delete');
    });

    // dialog
    Route::prefix('dialog')->name('dialog.')->group(function () {
        Route::get('list', [DialogController::class, 'list'])->name('list');
        Route::get('{dialogId}/detail', [DialogController::class, 'detail'])->name('detail');
        Route::get('{dialogId}/messages', [DialogController::class, 'messages'])->name('messages');
        Route::post('send-message', [DialogController::class, 'sendMessage'])->name('send.message');
        Route::put('mark-as-read', [DialogController::class, 'markAsRead'])->name('read');
        Route::delete('delete', [DialogController::class, 'delete'])->name('delete');
    });

    // group
    Route::prefix('group')->name('group.')->group(function () {
        Route::get('tree', [GroupController::class, 'tree'])->name('tree');
        Route::get('categories', [GroupController::class, 'categories'])->name('categories');
        Route::get('list', [GroupController::class, 'list'])->name('list');
        Route::get('{gid}/detail', [GroupController::class, 'detail'])->name('detail');
        Route::get('{gid}/interactive/{type}', [GroupController::class, 'interactive'])->name('interactive');
    });

    // hashtag
    Route::prefix('hashtag')->name('hashtag.')->group(function () {
        Route::get('list', [HashtagController::class, 'list'])->name('list');
        Route::get('{hid}/detail', [HashtagController::class, 'detail'])->name('detail');
        Route::get('{hid}/interactive/{type}', [HashtagController::class, 'interactive'])->name('interactive');
    });

    // post
    Route::prefix('post')->name('post.')->group(function () {
        Route::get('list', [PostController::class, 'list'])->name('list');
        Route::get('{pid}/detail', [PostController::class, 'detail'])->name('detail');
        Route::get('{pid}/interactive/{type}', [PostController::class, 'interactive'])->name('interactive');
        Route::get('{pid}/user-list', [PostController::class, 'userList'])->name('user.list');
        Route::get('{pid}/logs', [PostController::class, 'postLogs'])->name('logs');
        Route::get('{pid}/log/{logId}', [PostController::class, 'logDetail'])->name('log.detail');
        Route::delete('{pid}', [PostController::class, 'delete'])->name('delete');
        Route::get('follow/{type}', [PostController::class, 'follow'])->name('follow');
        Route::get('nearby', [PostController::class, 'nearby'])->name('nearby');
    });

    // comment
    Route::prefix('comment')->name('comment.')->group(function () {
        Route::get('list', [CommentController::class, 'list'])->name('list');
        Route::get('{cid}/detail', [CommentController::class, 'detail'])->name('detail');
        Route::get('{cid}/interactive/{type}', [CommentController::class, 'interactive'])->name('interactive');
        Route::get('{cid}/logs', [CommentController::class, 'commentLogs'])->name('logs');
        Route::get('{cid}/log/{logId}', [CommentController::class, 'logDetail'])->name('log.detail');
        Route::delete('{cid}', [CommentController::class, 'delete'])->name('delete');
    });

    // editor
    Route::prefix('editor')->name('editor.')->group(function () {
        Route::get('{type}/config', [EditorController::class, 'config'])->name('config');
        Route::get('{type}/drafts', [EditorController::class, 'drafts'])->name('drafts');
        Route::post('{type}/create', [EditorController::class, 'create'])->name('create');
        Route::post('{type}/generate/{fsid}', [EditorController::class, 'generate'])->name('generate');
        Route::get('{type}/{draftId}', [EditorController::class, 'detail'])->name('detail');
        Route::put('{type}/{draftId}', [EditorController::class, 'update'])->name('update');
        Route::post('direct-publish', [EditorController::class, 'directPublish'])->name('direct.publish');
        Route::post('{type}/{draftId}', [EditorController::class, 'publish'])->name('publish');
        Route::patch('{type}/{draftId}', [EditorController::class, 'revoke'])->name('revoke');
        Route::delete('{type}/{draftId}', [EditorController::class, 'delete'])->name('delete');
    });
});
