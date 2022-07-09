<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // index
    public function index(Request $request)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_comment_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_comment_query_config');

        return view('comments.index');
    }

    // list
    public function list(Request $request)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_comment_list_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_comment_list_query_config');

        return view('comments.list');
    }

    // nearby
    public function nearby(Request $request)
    {
        return view('comments.nearby');
    }

    // location
    public function location(Request $request)
    {
        return view('comments.location');
    }

    // likes
    public function likes(Request $request)
    {
        return view('comments.likes');
    }

    // dislikes
    public function dislikes(Request $request)
    {
        return view('comments.dislikes');
    }

    // following
    public function following(Request $request)
    {
        return view('comments.following');
    }

    // blocking
    public function blocking(Request $request)
    {
        return view('comments.blocking');
    }

    // detail
    public function detail(Request $request)
    {
        return view('comments.detail');
    }
}
