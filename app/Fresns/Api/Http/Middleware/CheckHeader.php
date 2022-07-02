<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\ApiException;
use App\Fresns\Api\Http\DTO\CheckHeaderDTO;
use App\Helpers\ConfigHelper;

class CheckHeader
{
    public function handle(Request $request, Closure $next)
    {
        $dtoHeaders = new CheckHeaderDTO(\request()->headers->all());
        $headers = $dtoHeaders->toArray();

        // 验证签名
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->verifySign($headers);

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->errorResponse();
        }

        // 配置
        $siteMode = ConfigHelper::fresnsConfigByItemKey('site_mode');
        $currentRouteName = \request()->route()->getName();

        // 账号登录状态
        $accountLogin = false;
        if ($headers['aid'] ?? null) {
            $accountLogin = true;
        }

        // 用户登录状态
        $userLogin = false;
        if ($headers['uid'] ?? null) {
            $userLogin = true;
        }

        // 账号白名单
        $accountWhitelist = match ($siteMode) {
            default => null,
            'public' => config('FsApiWhitelist.publicAccount'),
            'private' => config('FsApiWhitelist.privateAccount'),
        };

        // 用户白名单
        $userWhitelist = match ($siteMode) {
            default => null,
            'public' => config('FsApiWhitelist.publicUser'),
            'private' => config('FsApiWhitelist.privateUser'),
        };

        // 判断名单是否为空
        if (! $accountWhitelist || ! $userWhitelist) {
            throw new ApiException(33102);
        }

        // 未登录账号可访问的接口
        if (! $accountLogin && in_array($currentRouteName, $accountWhitelist)) {
            return $next($request);
        }

        // 未登录用户可访问的接口
        if (! $userLogin && in_array($currentRouteName, $userWhitelist)) {
            return $next($request);
        }

        // 登录状态
        if ($accountLogin && $userLogin) {
            return $next($request);
        }

        // 路由不在白名单 1 和 2 当中，也不是登录状态。
        throw new ApiException(31501);
    }
}
