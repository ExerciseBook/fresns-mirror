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
    // cache time
    public static function fresnsCacheTimeByFileType(?int $fileType = null)
    {
        if (empty($fileType)) {
            return now()->addHours();
        }

        $fileConfig = FileHelper::fresnsFileStorageConfigByType($fileType);

        if (! $fileConfig['antiLinkStatus']) {
            return now()->addHours();
        }

        $cacheTime = now()->addMinutes($fileConfig['antiLinkExpire']-1);

        return $cacheTime;
    }

    // forget crontab items
    public static function forgetCrontabItems()
    {
        Cache::forget('fresns_crontab_items');

        return;
    }

    // forget default langTag
    public static function forgetDefaultLanguage()
    {
        Cache::forget('fresns_default_langTag');

        return;
    }

    // forget default timezone
    public static function forgetDefaultTimezone()
    {
        Cache::forget('fresns_default_timezone');

        return;
    }

    // forget lang tags
    public static function forgetLangTags()
    {
        Cache::forget('fresns_lang_tags');

        return;
    }

    // forget table column lang content.
    public static function forgetTableColumnLangContent(string $tableName, string $tableColumn, int $tableId, ?string $langTag = null)
    {
        $cacheKey = "fresns_{$tableName}_{$tableColumn}_{$tableId}_{$langTag}";

        Cache::forget($cacheKey);

        return;
    }

    /**
     * forget fresns model.
     *
     * fresns_model_account_{$aid}
     * fresns_model_user_{$uidOrUsername}
     * fresns_model_group_{$gid}
     * fresns_model_hashtag_{$hid}
     * fresns_model_post_{$pid}
     * fresns_model_comment_{$cid}
     * fresns_model_file_{$fid}
     * fresns_model_extend_{$eid}
     */
    public static function forgetModel(string $modelName, string $fsid)
    {
        $cacheKey = "fresns_model_{$modelName}_{$fsid}";

        Cache::forget($cacheKey);

        return;
    }

    // forget api account
    public static function forgetApiAccount(string $aid)
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        foreach ($langTagArr as $langTag) {
            $cacheKey = 'fresns_api_auth_account_'.$aid.'_'.$langTag;

            Cache::forget($cacheKey);
        }

        return;
    }

    // forget api user
    public static function forgetApiUser(?int $uid = null)
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        foreach ($langTagArr as $langTag) {
            $cacheKey = 'fresns_api_auth_user_'.$uid.'_'.$langTag;

            Cache::forget($cacheKey);
        }

        return;
    }

    // forget api user expire info
    public static function forgetApiUserExpireInfo(int $uid)
    {
        $cacheKey = 'fresns_api_user_'.$uid.'_expire_info';

        Cache::forget($cacheKey);
        Cache::forget('fresns_api_guest_expire_info');

        return;
    }

    // forget api user content view perm
    public static function forgetApiUserContentViewPerm(int $uid)
    {
        $cacheKey = 'fresns_api_'.$uid.'_content_view_perm';

        Cache::forget($cacheKey);

        return;
    }

    /**
     * forget api multilingual content.
     * 0 means all
     *
     * fresns_api_stickers_0_{$langTag}
     * fresns_api_groups_{$userId}_{$langTag}
     */
    public static function forgetApiMultilingualContent(string $type, int $id, string $content)
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        foreach ($langTagArr as $langTag) {
            $cacheKey = "fresns_api_{$type}_{$id}_{$content}_{$langTag}";

            Cache::forget($cacheKey);
        }

        return;
    }

    /**
     * forget api content.
     * 0 means all
     *
     * stickers: $type=stickers, $id=0, $content=all, fresns_api_stickers_0_all
     * groups: $type=groups, $id=0, $content=all, fresns_api_groups_0_all
     * groups: $type=groups, $id={userId}, $content=user, fresns_api_groups_1024_user
     */
    public static function forgetApiContent(string $type, int $id, string $content)
    {
        $cacheKey = "fresns_api_{$type}_{$id}_{$content}";

        Cache::forget($cacheKey);

        return;
    }
}
