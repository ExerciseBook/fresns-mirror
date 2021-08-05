<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_dialog_messages dialog_messages
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsDialogMessages'],
    function () {
        Route::get('/fresnsdialogmessages/index', 'AmControllerAdmin@index')->name('admin.fresnsdialogmessages.index');
        Route::post('/fresnsdialogmessages/store', 'AmControllerAdmin@store')->name('admin.fresnsdialogmessages.store');
        Route::post('/fresnsdialogmessages/update',
            'AmControllerAdmin@update')->name('admin.fresnsdialogmessages.update');
        Route::post('/fresnsdialogmessages/destroy',
            'AmControllerAdmin@destroy')->name('admin.fresnsdialogmessages.destroy');
        Route::get('/fresnsdialogmessages/detail',
            'AmControllerAdmin@detail')->name('admin.fresnsdialogmessages.detail');
        Route::get('/fresnsdialogmessages/export',
            'AmControllerAdmin@export')->name('admin.fresnsdialogmessages.export');
    });
