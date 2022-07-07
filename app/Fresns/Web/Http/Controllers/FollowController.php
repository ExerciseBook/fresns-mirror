<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use Illuminate\Http\Request;

class FollowController extends Controller
{
    // all posts
    public function allPosts(Request $request)
    {
        return view('follows.all-posts');
    }

    // user posts
    public function userPosts(Request $request)
    {
        return view('follows.user-posts');
    }

    // group posts
    public function groupPosts(Request $request)
    {
        return view('follows.group-posts');
    }

    // hashtag posts
    public function hashtagPosts(Request $request)
    {
        return view('follows.hashtag-posts');
    }

    // all comments
    public function allComments(Request $request)
    {
        return view('follows.all-comments');
    }

    // user comments
    public function userComments(Request $request)
    {
        return view('follows.user-comments');
    }

    // group comments
    public function groupComments(Request $request)
    {
        return view('follows.group-comments');
    }

    // hashtag comments
    public function hashtagComments(Request $request)
    {
        return view('follows.hashtag-comments');
    }
}
