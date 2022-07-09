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

class UserController extends Controller
{
    // index
    public function index(Request $request)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_user_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_user_query_config');

        $query = [];
        if (! empty($queryConfig)) {
            parse_str($queryConfig, $query);
        }

        $result = ApiHelper::make()->get('/api/v2/user/list', [
            'query' => $query,
        ]);

        $users = $result['data']['list'];

        // todo: 分页，封装
        $items = $users->toArray();
        $total = $result['data']['paginate']['total'];
        $pageSize = $result['data']['paginate']['pageSize'];
        $paginate = new \Illuminate\Pagination\LengthAwarePaginator(
            items: $items,
            total: $total,
            perPage: $pageSize,
            currentPage: \request('page'),
        );

        $paginate->withPath('/'.\request()->path())->withQueryString();

        $users = $paginate;

        return view('users.index', compact('users'));
    }

    // list
    public function list(Request $request)
    {
        $queryStatus = ConfigHelper::fresnsConfigByItemKey('menu_user_list_query_status');
        $queryConfig = ConfigHelper::fresnsConfigByItemKey('menu_user_list_query_config');

        // todo: 转换为数组参数，封装
        $query = [];
        if (! empty($queryConfig)) {
            parse_str($queryConfig, $query);
        }

        // 使用数据库配置的参数给接口
        $result = ApiHelper::make()->get('/api/v2/user/list', [
            'query' => $query,
        ]);

        // todo: 分页，封装
        $users = $result['data']['list'];

        $items = $users->toArray();
        $total = $result['data']['paginate']['total'];
        $pageSize = $result['data']['paginate']['pageSize'];
        $paginate = new \Illuminate\Pagination\LengthAwarePaginator(
            items: $items,
            total: $total,
            perPage: $pageSize,
            currentPage: \request('page'),
        );

        $paginate->withPath('/'.\request()->path())->withQueryString();

        $users = $paginate;

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
