<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // index
    public function index(Request $request)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_post_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_post_query_config');

        return view('posts.index');
    }

    // list
    public function list(Request $request)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_post_list_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_post_list_query_config');

        return view('posts.list');
    }

    // nearby
    public function nearby(Request $request)
    {
        return view('posts.nearby');
    }

    // location
    public function location(Request $request)
    {
        return view('posts.location');
    }

    // likes
    public function likes(Request $request)
    {
        return view('posts.likes');
    }

    // dislikes
    public function dislikes(Request $request)
    {
        return view('posts.dislikes');
    }

    // following
    public function following(Request $request)
    {
        return view('posts.following');
    }

    // blocking
    public function blocking(Request $request)
    {
        return view('posts.blocking');
    }

    // detail
    public function detail(Request $request, string $pid)
    {
        return view('posts.detail');
    }
}
