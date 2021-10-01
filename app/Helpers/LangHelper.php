<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LangHelper
{
    // Initialize language information
    public static function initLocale()
    {
        // Language Tags (langTag)
        // Leave blank to output the default language content
        // If no default language is queried, the first entry is output
        $locale = request()->header('langTag', 'zh-Hans');
        $locale = request()->input('lang', 'zh-Hans');
        App::setLocale($locale);
    }
}
