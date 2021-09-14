<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// Fresns Content API
Route::group(['prefix' => 'fresns', 'namespace' => '\App\Http\FresnsApi\Content'], function () {
    // Group
    Route::post('/group/trees', 'AmControllerApi@groupTrees')->name('api.content.groupTrees');
    Route::post('/group/lists', 'AmControllerApi@groupLists')->name('api.content.groupLists');
    Route::post('/group/detail', 'AmControllerApi@groupDetail')->name('api.content.groupDetail');
    // Hashtag
    Route::post('/hashtag/lists', 'AmControllerApi@hashtagLists')->name('api.content.hashtagLists');
    Route::post('/hashtag/detail', 'AmControllerApi@hashtagDetail')->name('api.content.hashtagDetail');
    // Post
    Route::post('/post/lists', 'AmControllerApi@postLists')->name('api.content.postLists');
    Route::post('/post/follows', 'AmControllerApi@postFollows')->name('api.content.postFollows');
    Route::post('/post/nearbys', 'AmControllerApi@postNearbys')->name('api.content.postNearbys');
    Route::post('/post/detail', 'AmControllerApi@postDetail')->name('api.content.postDetail');
    // Comment
    Route::post('/comment/lists', 'AmControllerApi@commentLists')->name('api.content.commentLists');
    Route::post('/comment/detail', 'AmControllerApi@commentDetail')->name('api.content.commentDetail');
});
