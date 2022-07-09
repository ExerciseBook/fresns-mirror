<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Helpers\ApiHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\StrHelper;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    // index
    public function index(Request $request)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_hashtag_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_hashtag_query_config');

        $query = [];
        if (! empty($queryConfig)) {
            parse_str($queryConfig, $query);
        }

        $result = ApiHelper::make()->get('/api/v2/hashtag/list', [
            'query' => $query,
        ]);

        $hashtags = $result['data']['list']->toArray();

        return view('hashtags.index', compact('hashtags'));
    }

    // list
    public function list(Request $request)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_hashtag_list_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_hashtag_list_query_config');

        $query = [];
        if (! empty($queryConfig)) {
            parse_str($queryConfig, $query);
        }

        $result = ApiHelper::make()->get('/api/v2/hashtag/list', [
            'query' => $query,
        ]);

        $hashtags = $result['data']['list']->toArray();

        return view('hashtags.list', compact('hashtags'));
    }

    // likes
    public function likes(Request $request)
    {
        return view('hashtags.likes');
    }

    // dislikes
    public function dislikes(Request $request)
    {
        return view('hashtags.dislikes');
    }

    // following
    public function following(Request $request)
    {
        return view('hashtags.following');
    }

    // blocking
    public function blocking(Request $request)
    {
        return view('hashtags.blocking');
    }

    // detail
    public function detail(Request $request, string $hid)
    {
        $result = ApiHelper::make()->get("/api/v2/hashtag/{$hid}/detail");

        $items = $result['data']['items']->toArray();
        $hashtag = $result['data']['detail']->toArray();

        return view('hashtags.detail', compact('items', 'hashtag'));
    }
}
