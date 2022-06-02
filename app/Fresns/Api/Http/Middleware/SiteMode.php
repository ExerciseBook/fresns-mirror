<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Middleware;

use App\Helpers\ConfigHelper;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use App\Utilities\PermissionUtility;
use App\Exceptions\ApiException;

class SiteMode
{
    public function handle(Request $request, Closure $next)
    {
        // - 站点为私有模式时，需要验证用户到期状态 users.expired_at，确认用户是否已到期。
        // 并根据站点配置的到期后数据处理模式 configs.site_private_end 对数据进行处理。
        //     - 站点到期数据处理模式为 1 时，接口不允许请求。
        //     - 站点到期数据处理模式为 2 时，输出用户到期前的内容，到期之后的内容不进行输出展示。

        // 站点是否是私有模式
        $isPrivateStatus = ConfigHelper::fresnsConfigByItemKey('site_private_status');

        // 未开启私有模式，直接放行
        if (!$isPrivateStatus) {
            return $next($request);
        }

        // 获取当前登录的用户
        /** @var User */
        $user = auth()->user();

        // 获取用户的过期状态：1，2，3
        $expiredStatus = $user->getCurrentExpredStatus();

        // 站点是私有模式时, 如果站点到期状态为1, 不允许请求
        $userConfig = PermissionUtility::getUserExpireInfo($user->id);
        if (! $userConfig['userStatus'] && $userConfig['expireAfter'] == 1) {
            throw new ApiException(35302);
        }


        // 获取当前路由名
        $currentRouteName = \request()->route()->getName();

        // 名单 1 接口，仅用户状态 3 可访问
        $whistle1 = [
            'posts.list',
        ];
        // 名单 2 接口，仅用户状态 2 和 3 可访问
        $whistle2 = [];

        // 名单 1 接口，仅用户状态 3 可访问
        if (in_array($currentRouteName, $whistle1) && !in_array($expiredStatus, [3])) {
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
