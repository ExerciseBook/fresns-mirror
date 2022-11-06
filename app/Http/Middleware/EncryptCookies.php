<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        'timezone',
        'fs_aid',
        'fs_aid_token',
        'fs_uid',
        'fs_uid_token',
        'lang', // 管理后台使用
        'langTag', // 引擎 url 多语言使用
    ];
}
