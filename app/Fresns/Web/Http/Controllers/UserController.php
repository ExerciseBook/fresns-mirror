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

class UserController extends Controller
{
    // index
    public function index(Request $request)
    {
        // 系统配置参数与用户参数处理
        $query = QueryHelper::convertOptionToRequestParam('user', $request->all());

        $result = ApiHelper::make()->get('/api/v2/user/list', [
            'query' => $query,
        ]);

        // 分页
        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('users.index', compact('users'));
    }

    // list
    public function list(Request $request)
    {
        // 系统配置参数与用户参数处理
        $query = QueryHelper::convertOptionToRequestParam('user_list', $request->all());

        $result = ApiHelper::make()->get('/api/v2/user/list', [
            'query' => $query,
        ]);

        // 分页，封装
        $users = QueryHelper::convertApiDataToPaginate(
            items: $result['data']['list'],
            paginate: $result['data']['paginate'],
        );

        return view('users.list', compact('users'));
    }

    // likes
    public function likes(Request $request)
    {
        $uid = fs_user('uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/like/users");

        $users = $result['data']['list'];

        return view('users.likes', compact('users'));
    }

    // dislikes
    public function dislikes(Request $request)
    {
        $uid = fs_user('uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/dislike/users");

        $users = $result['data']['list'];

        return view('users.dislikes', compact('users'));
    }

    // following
    public function following(Request $request)
    {
        $uid = fs_user('uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/follow/users");

        $users = $result['data']['list'];

        return view('users.following', compact('users'));
    }

    // blocking
    public function blocking(Request $request)
    {
        $uid = fs_user('uid');

        $result = ApiHelper::make()->get("/api/v2/user/{$uid}/mark/block/users");

        $users = $result['data']['list'];

        return view('users.blocking', compact('users'));
    }
}
