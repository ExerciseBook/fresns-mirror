<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_domain_links domain_links
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsDomainLinks'],
    function () {
        Route::get('/fresnsdomainlinks/index', 'AmControllerAdmin@index')->name('admin.fresnsdomainlinks.index');
        Route::post('/fresnsdomainlinks/store', 'AmControllerAdmin@store')->name('admin.fresnsdomainlinks.store');
        Route::post('/fresnsdomainlinks/update', 'AmControllerAdmin@update')->name('admin.fresnsdomainlinks.update');
        Route::post('/fresnsdomainlinks/destroy', 'AmControllerAdmin@destroy')->name('admin.fresnsdomainlinks.destroy');
        Route::get('/fresnsdomainlinks/detail', 'AmControllerAdmin@detail')->name('admin.fresnsdomainlinks.detail');
        Route::get('/fresnsdomainlinks/export', 'AmControllerAdmin@export')->name('admin.fresnsdomainlinks.export');
    });
