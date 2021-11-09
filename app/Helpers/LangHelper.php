<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LangHelper
{
    // Initialize language information
    public static function initLocale()
    {
        // Language Tags (langTag)
        // Leave blank to output the default language content
        // If no default language is queried, the first entry is output
        $input_locale = request()->input('lang', '');
        $cache_locale = Cache::get('install_lang');
        if($cache_locale){
            App::setLocale($cache_locale);
        } elseif ($input_locale){
            App::setLocale($input_locale);
        }
    }
}
