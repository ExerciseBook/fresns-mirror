<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Controllers;

use App\Fresns\Web\Exceptions\ErrorException;
use App\Fresns\Web\Helpers\ApiHelper;
use App\Fresns\Web\Helpers\QueryHelper;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // index
    public function index(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_POST, $request->all());

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'posts' => $client->getAsync('/api/v2/post/list', [
                'query' => $query,
            ]),
            'sticky' => $client->getAsync('/api/v2/post/list', [
                'query' => [
                    'stickyState' => 3,
                ],
            ]),
        ]);

        if (data_get($results, 'posts.code') !== 0) {
            throw new ErrorException($results['posts']['message'], $results['posts']['code']);
        }

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            paginate: $results['posts']['data']['paginate'],
        );

        return view('posts.index', compact('posts', 'sticky'));
    }

    // list
    public function list(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_POST_LIST, $request->all());

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'posts' => $client->getAsync('/api/v2/post/list', [
                'query' => $query,
            ]),
            'sticky' => $client->getAsync('/api/v2/post/list', [
                'query' => [
                    'stickyState' => 3,
                ],
            ]),
        ]);

        if (data_get($results, 'posts.code') !== 0) {
            throw new ErrorException($results['posts']['message'], $results['posts']['code']);
        }

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $results['posts']['data']['list'],
            paginate: $results['posts']['data']['paginate'],
        );

        return view('posts.list', compact('posts', 'sticky'));
    }

    // nearby
    public function nearby(Request $request)
    {
        if (empty($request->mapLng) || empty($request->mapLat)) {
            $result = [
                'code' => 0,
                'message' => 'success',
                'data' => [
                    'paginate' => [
                        'total' => 0,
                        'pageSize' => 15,
                        'currentPage' => 1,
                        'lastPage' => 1,
                    ],
                    'list' => [],
                ]
            ];

            $posts = QueryHelper::convertApiDataToPaginate(
                items: $result['data']['list'],
                paginate: $result['data']['paginate'],
            );

            return view('posts.nearby', compact('posts'));
        }

        $query = $request->all();
        $query['mapId'] = $request->mapId;
        $query['mapLng'] = $request->mapLng;
        $query['mapLat'] = $request->mapLat;
        $query['unit'] = $request->unit ?? null;
        $query['length'] = $request->length ?? null;

        $result = ApiHelper::make()->get('/api/v2/post/nearby', [
            'query' => $query,
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('posts.nearby', compact('posts'));
    }

    // location
    public function location(Request $request)
    {
        if (empty($request->mapLng) || empty($request->mapLat)) {
            return back()->with([
                'failure' => fs_lang('location').': '.fs_lang('errorEmpty'),
            ]);
        }

        $query = $request->all();
        $query['mapId'] = $request->mapId;
        $query['mapLng'] = $request->mapLng;
        $query['mapLat'] = $request->mapLat;
        $query['unit'] = $request->unit ?? null;
        $query['length'] = $request->length ?? null;

        $result = ApiHelper::make()->get('/api/v2/post/nearby', [
            'query' => $query,
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('posts.location', compact('posts'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/like/posts", [
            'query' => $request->all(),
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('posts.likes', compact('posts'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/dislike/posts", [
            'query' => $request->all(),
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('posts.dislikes', compact('posts'));
    }

    // following
    public function following(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/follow/posts", [
            'query' => $request->all(),
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('posts.following', compact('posts'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = fs_user('detail.uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/block/posts", [
            'query' => $request->all(),
        ]);

        $posts = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('posts.blocking', compact('posts'));
    }

    // detail
    public function detail(Request $request, string $pid)
    {
        $query = $request->all();
        $query['pid'] = $pid;

        $client = ApiHelper::make();

        $results = $client->unwrapRequests([
            'post' => $client->getAsync("/api/v2/post/{$pid}/detail"),
            'comments' => $client->getAsync('/api/v2/comment/list', [
                'query' => $query,
            ]),
            'sticky' => $client->getAsync('/api/v2/comment/list', [
                'query' => [
                    'pid' => $pid,
                    'sticky' => true,
                ],
            ]),
        ]);

        if ($results['post']['code'] != 0) {
            throw new ErrorException($results['post']['message'], $results['post']['code']);
        }

        $items = $results['post']['data']['items'];
        $post = $results['post']['data']['detail'];

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $results['comments']['data']['list'],
            paginate: $results['comments']['data']['paginate'],
        );

        return view('posts.detail', compact('items', 'post', 'comments', 'sticky'));
    }
}
