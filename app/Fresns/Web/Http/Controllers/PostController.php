<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Helpers\ApiHelper;
use App\Helpers\ConfigHelper;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // index
    public function index(Request $request)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_post_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_post_query_config');

        $query = [];
        if (! empty($queryConfig)) {
            parse_str($queryConfig, $query);
        }

        $result = ApiHelper::make()->get('/api/v2/post/list', [
            'query' => $query,
        ]);

        $posts = $result['data']['list']->toArray();

        return view('posts.index', compact('posts'));
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
    public function location(Request $request, int $mapId, string $mapLng, string $mapLat)
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
        $result = ApiHelper::make()->get("/api/v2/post/{$pid}/detail");

        $items = $result['data']['items']->toArray();
        $post = $result['data']['detail']->toArray();

        return view('posts.detail', compact('items', 'post'));
    }
}
