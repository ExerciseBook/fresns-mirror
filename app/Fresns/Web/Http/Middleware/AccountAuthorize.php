<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Http\Middleware;

use App\Utilities\ConfigUtility;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Response;

class AccountAuthorize
{
    public function handle(Request $request, Closure $next)
    {
        try {
            if (fs_account()->check()) {
                return $next($request);
            } else {
                $langTag = current_lang_tag() ?? '';
                $accountLoginTip = ConfigUtility::getCodeMessage(31501, 'Fresns', $langTag);

                return $this->shouldLoginRender($accountLoginTip);
            }
        } catch (\Exception $exception) {
            return $this->shouldLoginRender($exception->getMessage());
        }
    }

    public function shouldLoginRender(string $message, int $code = 401)
    {
        if (request()->ajax()) {
            return Response::json(compact('message', $code), 401);
        } else {
            return redirect(fs_route(route('fresns.account.login')))->withErrors($message);
        }
    }
}
