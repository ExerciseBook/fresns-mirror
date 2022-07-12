<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Helpers\ApiHelper;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // posts
    public function posts(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.posts', compact('items', 'user'));
    }

    // comments
    public function comments(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.comments', compact('items', 'user'));
    }

    // likers
    public function likers(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.likers', compact('items', 'user'));
    }

    // dislikers
    public function dislikers(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.dislikers', compact('items', 'user'));
    }

    // followers
    public function followers(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.followers', compact('items', 'user'));
    }

    // blockers
    public function blockers(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.blockers', compact('items', 'user'));
    }

    /**
     * like.
     */

    // likeUsers
    public function likeUsers(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.likes.users', compact('items', 'user'));
    }

    // likeGroups
    public function likeGroups(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.likes.groups', compact('items', 'user'));
    }

    // likeHashtags
    public function likeHashtags(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.likes.hashtags', compact('items', 'user'));
    }

    // likePosts
    public function likePosts(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.likes.posts', compact('items', 'user'));
    }

    // likeComments
    public function likeComments(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.likes.comments', compact('items', 'user'));
    }

    /**
     * dislike.
     */

    // dislikeUsers
    public function dislikeUsers(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.dislikes.users', compact('items', 'user'));
    }

    // dislikeGroups
    public function dislikeGroups(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.dislikes.groups', compact('items', 'user'));
    }

    // dislikeHashtags
    public function dislikeHashtags(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.dislikes.hashtags', compact('items', 'user'));
    }

    // dislikePosts
    public function dislikePosts(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.dislikes.posts', compact('items', 'user'));
    }

    // dislikeComments
    public function dislikeComments(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.dislikes.comments', compact('items', 'user'));
    }

    /**
     * following.
     */

    // followingUsers
    public function followingUsers(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.following.users', compact('items', 'user'));
    }

    // followingGroups
    public function followingGroups(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.following.groups', compact('items', 'user'));
    }

    // followingHashtags
    public function followingHashtags(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.following.hashtags', compact('items', 'user'));
    }

    // followingPosts
    public function followingPosts(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.following.posts', compact('items', 'user'));
    }

    // followingComments
    public function followingComments(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.following.comments', compact('items', 'user'));
    }

    /**
     * blocking.
     */

    // blockingUsers
    public function blockingUsers(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.blocking.users', compact('items', 'user'));
    }

    // blockingGroups
    public function blockingGroups(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.blocking.groups', compact('items', 'user'));
    }

    // blockingHashtags
    public function blockingHashtags(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.blocking.hashtags', compact('items', 'user'));
    }

    // blockingPosts
    public function blockingPosts(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.blocking.posts', compact('items', 'user'));
    }

    // blockingComments
    public function blockingComments(Request $request, string $uidOrUsername)
    {
        $result = ApiHelper::make()->get("/api/v2/user/{$uidOrUsername}/detail");

        $items = $result['data']['items'];
        $user = $result['data']['detail'];

        return view('profile.blocking.comments', compact('items', 'user'));
    }
}
