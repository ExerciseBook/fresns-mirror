<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // posts
    public function posts(Request $request, string $uidOrUsername)
    {
        return view('profile.posts');
    }

    // comments
    public function comments(Request $request, string $uidOrUsername)
    {
        return view('profile.comments');
    }

    // likers
    public function likers(Request $request, string $uidOrUsername)
    {
        return view('profile.likers');
    }

    // dislikers
    public function dislikers(Request $request, string $uidOrUsername)
    {
        return view('profile.dislikers');
    }

    // followers
    public function followers(Request $request, string $uidOrUsername)
    {
        return view('profile.followers');
    }

    // blockers
    public function blockers(Request $request, string $uidOrUsername)
    {
        return view('profile.blockers');
    }

    /**
     * like.
     */

    // likeUsers
    public function likeUsers(Request $request, string $uidOrUsername)
    {
        return view('profile.likes.users');
    }

    // likeGroups
    public function likeGroups(Request $request, string $uidOrUsername)
    {
        return view('profile.likes.groups');
    }

    // likeHashtags
    public function likeHashtags(Request $request, string $uidOrUsername)
    {
        return view('profile.likes.hashtags');
    }

    // likePosts
    public function likePosts(Request $request, string $uidOrUsername)
    {
        return view('profile.likes.posts');
    }

    // likeComments
    public function likeComments(Request $request, string $uidOrUsername)
    {
        return view('profile.likes.comments');
    }

    /**
     * dislike.
     */

    // dislikeUsers
    public function dislikeUsers(Request $request, string $uidOrUsername)
    {
        return view('profile.dislikes.users');
    }

    // dislikeGroups
    public function dislikeGroups(Request $request, string $uidOrUsername)
    {
        return view('profile.dislikes.groups');
    }

    // dislikeHashtags
    public function dislikeHashtags(Request $request, string $uidOrUsername)
    {
        return view('profile.dislikes.hashtags');
    }

    // dislikePosts
    public function dislikePosts(Request $request, string $uidOrUsername)
    {
        return view('profile.dislikes.posts');
    }

    // dislikeComments
    public function dislikeComments(Request $request, string $uidOrUsername)
    {
        return view('profile.dislikes.comments');
    }

    /**
     * following.
     */

    // followingUsers
    public function followingUsers(Request $request, string $uidOrUsername)
    {
        return view('profile.following.users');
    }

    // followingGroups
    public function followingGroups(Request $request, string $uidOrUsername)
    {
        return view('profile.following.groups');
    }

    // followingHashtags
    public function followingHashtags(Request $request, string $uidOrUsername)
    {
        return view('profile.following.hashtags');
    }

    // followingPosts
    public function followingPosts(Request $request, string $uidOrUsername)
    {
        return view('profile.following.posts');
    }

    // followingComments
    public function followingComments(Request $request, string $uidOrUsername)
    {
        return view('profile.following.comments');
    }

    /**
     * blocking.
     */

    // blockingUsers
    public function blockingUsers(Request $request, string $uidOrUsername)
    {
        return view('profile.blocking.users');
    }

    // blockingGroups
    public function blockingGroups(Request $request, string $uidOrUsername)
    {
        return view('profile.blocking.groups');
    }

    // blockingHashtags
    public function blockingHashtags(Request $request, string $uidOrUsername)
    {
        return view('profile.blocking.hashtags');
    }

    // blockingPosts
    public function blockingPosts(Request $request, string $uidOrUsername)
    {
        return view('profile.blocking.posts');
    }

    // blockingComments
    public function blockingComments(Request $request, string $uidOrUsername)
    {
        return view('profile.blocking.comments');
    }
}
