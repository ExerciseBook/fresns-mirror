<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Middleware;

use App\Fresns\Api\Traits\ApiHeaderTrait;
use App\Exceptions\ApiException;
use App\Helpers\ConfigHelper;
use Closure;
use Illuminate\Http\Request;

class CheckSiteModel
{
    use ApiHeaderTrait;

    public function handle(Request $request, Closure $next)
    {
        $modeConfig = ConfigHelper::fresnsConfigByItemKeys([
            'site_mode',
            'site_private_end_after',
        ]);

        if ($modeConfig['site_mode'] == 'public') {
            return $next($request);
        }

        $authUser = $this->user();

        if (empty($authUser)) {
            throw new ApiException(31601);
        }

        if (empty($authUser->expired_at)) {
            throw new ApiException(35306);
        }

        $now = time();
        $expireTime = strtotime($authUser->expired_at);

        if ($modeConfig['site_private_end_after'] == 1 && $expireTime < $now) {
            throw new ApiException(35303);
        }

        return $next($request);
    }
}
