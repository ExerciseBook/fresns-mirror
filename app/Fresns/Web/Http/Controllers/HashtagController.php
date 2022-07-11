<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Helpers\ApiHelper;
use App\Fresns\Web\Helpers\QueryHelper;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    // index
    public function index(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam('hashtag', $request->all());

        $result = ApiHelper::make()->get('/api/v2/hashtag/list', [
            'query' => $query,
        ]);

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('hashtags.list', compact('hashtags'));
    }

    // list
    public function list(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam('hashtag_list', $request->all());

        $result = ApiHelper::make()->get('/api/v2/hashtag/list', [
            'query' => $query,
        ]);

        $hashtags = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

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
