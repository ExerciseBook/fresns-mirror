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

class CommentController extends Controller
{
    // index
    public function index(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_COMMENT, $request->all());

        $result = ApiHelper::make()->get('/api/v2/comment/list', [
            'query' => $query,
        ]);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('comments.index', compact('comments'));
    }

    // list
    public function list(Request $request)
    {
        $query = QueryHelper::convertOptionToRequestParam(QueryHelper::TYPE_COMMENT_LIST, $request->all());

        $result = ApiHelper::make()->get('/api/v2/comment/list', [
            'query' => $query,
        ]);

        $comments = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('comments.list', compact('comments'));
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
