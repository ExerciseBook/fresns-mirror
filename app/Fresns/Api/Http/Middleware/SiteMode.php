<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SiteMode
{
    public function handle(Request $request, Closure $next)
    {
        $isPrivateStatus = false;

        // 未开启私有模式，直接放行
        if (!$isPrivateStatus) {
            return $next($request);
        }

        // 获取当前登录的用户
        $users = auth()->user();

        // 获取用户的过期状态：1，2，3
        $expiredStatus = $user->getCurrentExpredStatus();

        // 获取当前路由名
        $currentRouteName = \request()->route()->getName();

        // 名单 1 接口，仅用户状态 3 可访问
        $whistle1 = [
            'posts.list',
        ];
        // 名单 2 接口，仅用户状态 2 和 3 可访问
        $whistle2 = [];

        // 名单 1 接口，仅用户状态 3 可访问
        if (in_array($currentRouteName, $whistle1) && !in_array($expiredStatus, 3)) {
            return throw new \RuntimeException("当前不是会员 3，不能访问");
        }

        // 名单 2 接口，仅用户状态 2 和 3 可访问
        if (in_array($currentRouteName, $whistle2) && !in_array($expiredStatus, [2, 3])) {
            return throw new \RuntimeException("当前不是会员 2 或会员 3，不能访问");
        }

        // 当前接口不在 名单 1、名单 2中，直接放行
        return $next($request);
    }
}
