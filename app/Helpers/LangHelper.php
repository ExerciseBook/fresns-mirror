<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

/*
 * 语言处理类
 */

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LangHelper
{
    // 初始化语言信息
    public static function initLocale()
    {

        // 语言标签（留空则输出默认语言内容，查询不到默认语言则输出第一条）
        $locale = request()->header('langTag', 'zh-Hans');

        $locale = request()->input('lang', 'zh-Hans');

        App::setLocale($locale);
    }
}
