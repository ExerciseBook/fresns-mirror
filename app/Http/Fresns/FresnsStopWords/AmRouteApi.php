<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_stop_words stop_words
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsStopWords'],
    function () {
        Route::get('/fresnsstopwords/index', 'AmControllerAdmin@index')->name('admin.fresnsstopwords.index');
        Route::post('/fresnsstopwords/store', 'AmControllerAdmin@store')->name('admin.fresnsstopwords.store');
        Route::post('/fresnsstopwords/update', 'AmControllerAdmin@update')->name('admin.fresnsstopwords.update');
        Route::post('/fresnsstopwords/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsstopwords.destroy');
        Route::get('/fresnsstopwords/detail', 'AmControllerAdmin@detail')->name('admin.fresnsstopwords.detail');
        Route::get('/fresnsstopwords/export', 'AmControllerAdmin@export')->name('admin.fresnsstopwords.export');
    });
