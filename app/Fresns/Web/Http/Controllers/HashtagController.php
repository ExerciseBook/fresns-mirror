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
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_HASHTAG, $request->all());

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
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_HASHTAG_LIST, $request->all());

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
        $query = $request->all();
        $query['hid'] = $hid;

        $client = ApiHelper::make();

        $results = $client->unwrap([
            'hashtag' => $client->getAsync("/api/v2/hashtag/{$hid}/detail"),
            'posts'   => $client->getAsync('/api/v2/post/list', [
                'query' => $query,
            ]),
        ]);

        $items = $results['hashtag']['data']['items'];
        $hashtag = $results['hashtag']['data']['detail'];

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            paginate: $results['posts']['data']['paginate'],
        );

        return view('hashtags.detail', compact('items', 'hashtag', 'posts'));
    }
}
