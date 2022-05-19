<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Header
{
    public function handle(Request $request, Closure $next)
    {
        $headers = \request()->headers->all();
        $login = false;

        $currentRouteName = \request()->route()->getName();
        $whistle1 = [
            'posts.list',
        ];
        $whistle2 = [];

        // 默认校验全部 header
        $filterWhistleHeaders = $headers;

        // 未登录时，白名单1，去除 aid uid token 后进行签名验证
        if (!$login && in_array($currentRouteName, $whistle1)) {
            $filterWhistleHeaders = collect($headers)->except(['aid', 'uid', 'token'])->all();
        }

        // 未登录时，白名单2，去除 aid token 后进行签名验证
        if (!$login && in_array($currentRouteName, $whistle2)) {
            $filterWhistleHeaders = collect($headers)->except(['aid', 'token'])->all();
        }

        // 验证过滤后的 header 签名
        $validateResult = \FresnsCmdWord::plugin()->verifyHeaderSign($filterWhistleHeaders);

        // 验签未通过，拦截
        if ($validateResult->isErrorResponse()) {
            throw new \RuntimeException('未登录，签名验证失败');
        }

        // 验签通过，放行
        return $next($request);
    }
}
