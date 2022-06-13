<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    // default langTag
    public static function fresnsDefaultLanguage()
    {
        Cache::forget('fresns_default_langTag');

        return;
    }

    // default timezone
    public static function fresnsDefaultTimezone()
    {
        Cache::forget('fresns_default_timezone');

        return;
    }

    // lang tags
    public static function fresnsLangTags()
    {
        Cache::forget('fresns_lang_tags');

        return;
    }

    // fresns api stickers
    public static function fresnsApiStickers()
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        foreach ($langTagArr as $langTag) {
            $cacheKey = 'fresns_api_stickers_'.$langTag;

            Cache::forget($cacheKey);
        }

        return;
    }

    // fresns api account
    public static function fresnsApiAccount(string $aid)
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        foreach ($langTagArr as $langTag) {
            $cacheKey = 'fresns_api_auth_account_'.$aid.'_'.$langTag;

            Cache::forget($cacheKey);
        }

        return;
    }

    // fresns api user
    public static function fresnsApiUser(?int $uid = null)
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        foreach ($langTagArr as $langTag) {
            $cacheKey = 'fresns_api_auth_user_'.$uid.'_'.$langTag;

            Cache::forget($cacheKey);
        }

        return;
    }

    // fresns api user expire info
    public static function fresnsApiUserExpireInfo(int $uid)
    {
        $cacheKey = 'fresns_api_user_'.$uid.'_expire_info';

        Cache::forget($cacheKey);
        Cache::forget('fresns_api_guest_expire_info');

        return;
    }

    // fresns api user content view perm
    public static function fresnsApiUserContentViewPerm(int $uid)
    {
        $cacheKey = 'fresns_api_'.$uid.'_content_view_perm';

        Cache::forget($cacheKey);

        return;
    }

    // fresns api content
    public static function fresnsApiContent(string $type, string $content, int $id)
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        foreach ($langTagArr as $langTag) {
            $cacheKey = "fresns_api_{$type}_{$content}_{$id}_{$langTag}";

            Cache::forget($cacheKey);
        }

        return;
    }
}
