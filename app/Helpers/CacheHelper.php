<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\Config;
use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public static function fresnsDefaultLanguage()
    {
        Cache::forget('fresns_default_langTag');

        return;
    }

    public static function fresnsDefaultTimezone()
    {
        Cache::forget('fresns_default_timezone');

        return;
    }

    public static function fresnsApiAuthAccount()
    {
        $aid = \request()->header('aid');

        $langArr = Config::where('item_key', 'language_menus')->value('item_value');
        $langArrCollection = collect($langArr)->pluck('langTag');

        foreach ($langArrCollection as $langTag) {
            $cacheKey = 'fresns_api_auth_account_'.$aid.'_'.$langTag;

            Cache::forget($cacheKey);
        }

        return;
    }

    public static function fresnsApiAuthUser()
    {
        $uid = \request()->header('uid');

        $langArr = Config::where('item_key', 'language_menus')->value('item_value');
        $langArrCollection = collect($langArr)->pluck('langTag');

        foreach ($langArrCollection as $langTag) {
            $cacheKey = 'fresns_api_auth_user_'.$uid.'_'.$langTag;

            Cache::forget($cacheKey);
        }

        return;
    }

    public static function fresnsApiContent(string $type, string $content, int $id)
    {
        $langArr = Config::where('item_key', 'language_menus')->value('item_value');
        $langArrCollection = collect($langArr)->pluck('langTag');

        foreach ($langArrCollection as $langTag) {
            $cacheKey = "fresns_api_{$type}_{$content}_{$id}_{$langTag}";

            Cache::forget($cacheKey);
        }

        return;
    }
}
