<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns Content API
Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\FresnsApi\Content'], function () {
    // Group
    Route::post('/group/trees', 'AmControllerApi@trees')->name('admin.content.trees');
    Route::post('/group/lists', 'AmControllerApi@group_lists')->name('admin.content.group_lists');
    Route::post('/group/detail', 'AmControllerApi@group_detail')->name('admin.content.group_detail');
    // Hashtag
    Route::post('/hashtag/lists', 'AmControllerApi@hashtag_lists')->name('admin.content.hashtag_lists');
    Route::post('/hashtag/detail', 'AmControllerApi@hashtag_detail')->name('admin.content.hashtag_detail');
    // Post
    Route::post('/post/lists', 'AmControllerApi@post_lists')->name('admin.content.post_lists');
    Route::post('/post/detail', 'AmControllerApi@post_detail')->name('admin.content.post_detail');
    Route::post('/post/follows', 'AmControllerApi@postFollows')->name('admin.content.postFollows');
    Route::post('/post/nearbys', 'AmControllerApi@postNearbys')->name('admin.content.postNearbys');
    // Comment
    Route::post('/comment/lists', 'AmControllerApi@comment_lists')->name('admin.content.comment_lists');
    Route::post('/comment/detail', 'AmControllerApi@commentDetail')->name('admin.content.commentDetail');
});
