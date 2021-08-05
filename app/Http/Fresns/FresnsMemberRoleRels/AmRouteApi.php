<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns_members_role_rels member_role_rels
Route::group(['prefix' => 'admin', 'middleware' => 'auth:api', 'namespace' => '\App\Http\Fresns\FresnsMemberRoleRels'],
    function () {
        Route::get('/fresnsmemberrolerels/index', 'AmControllerAdmin@index')->name('admin.fresnsmemberrolerels.index');
        Route::post('/fresnsmemberrolerels/store', 'AmControllerAdmin@store')->name('admin.fresnsmemberrolerels.store');
        Route::post('/fresnsmemberrolerels/update',
            'AmControllerAdmin@update')->name('admin.fresnsmemberrolerels.update');
        Route::post('/fresnsmemberrolerels/destroy',
            'AmControllerAdmin@destroy')->name('admin.fresnsmemberrolerels.destroy');
        Route::get('/fresnsmemberrolerels/detail',
            'AmControllerAdmin@detail')->name('admin.fresnsmemberrolerels.detail');
        Route::get('/fresnsmemberrolerels/export',
            'AmControllerAdmin@export')->name('admin.fresnsmemberrolerels.export');
    });
