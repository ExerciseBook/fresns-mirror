<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns Message API
Route::group(['prefix' => '/fresns', 'namespace' => '\App\Http\FresnsApi\Messages'], function () {
    // Notify
    Route::post('/notify/lists', 'AmControllerApi@lists')->name('tweet.AmControllerApi.lists');
    Route::post('/notify/read', 'AmControllerApi@read')->name('tweet.AmControllerApi.read');
    Route::post('/notify/delete', 'AmControllerApi@delete')->name('tweet.AmControllerApi.delete');
    // Dialog
    Route::post('/dialog/lists', 'AmControllerApi@dialog_lists')->name('tweet.AmControllerApi.dialog_lists');
    Route::post('/dialog/messages', 'AmControllerApi@message_lists')->name('tweet.AmControllerApi.message_lists');
    Route::post('/dialog/read', 'AmControllerApi@message_read')->name('tweet.AmControllerApi.message_read');
    Route::post('/dialog/send', 'AmControllerApi@message_send')->name('tweet.AmControllerApi.message_send');
    Route::post('/dialog/delete', 'AmControllerApi@dialog_delete')->name('tweet.AmControllerApi.dialog_delete');
});
