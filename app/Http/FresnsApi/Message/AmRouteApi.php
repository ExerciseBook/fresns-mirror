<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns Message API
Route::group(['prefix' => '/fresns', 'namespace' => '\App\Http\FresnsApi\Message'], function () {
    // Notify
    Route::post('/notify/lists', 'AmControllerApi@notifyLists')->name('api.notify.lists');
    Route::post('/notify/read', 'AmControllerApi@notifyRead')->name('api.notify.read');
    Route::post('/notify/delete', 'AmControllerApi@notifyDelete')->name('api.notify.delete');
    // Dialog
    Route::post('/dialog/lists', 'AmControllerApi@dialogLists')->name('api.dialog.lists');
    Route::post('/dialog/messages', 'AmControllerApi@dialogMessages')->name('api.dialog.messages');
    Route::post('/dialog/read', 'AmControllerApi@readMessage')->name('api.dialog.readMessage');
    Route::post('/dialog/send', 'AmControllerApi@sendMessage')->name('api.dialog.sendMessage');
    Route::post('/dialog/delete', 'AmControllerApi@dialogDelete')->name('api.dialog.delete');
});
