<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_implants implants
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsImplants'],
    function () {
        Route::get('/fresnsimplants/index', 'AmControllerAdmin@index')->name('admin.fresnsimplants.index');
        Route::post('/fresnsimplants/store', 'AmControllerAdmin@store')->name('admin.fresnsimplants.store');
        Route::post('/fresnsimplants/update', 'AmControllerAdmin@update')->name('admin.fresnsimplants.update');
        Route::post('/fresnsimplants/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsimplants.destroy');
        Route::get('/fresnsimplants/detail', 'AmControllerAdmin@detail')->name('admin.fresnsimplants.detail');
        Route::get('/fresnsimplants/export', 'AmControllerAdmin@export')->name('admin.fresnsimplants.export');
    });
