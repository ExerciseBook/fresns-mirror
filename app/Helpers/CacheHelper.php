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
            $digital = rand(6, 72);

            return now()->addHours($digital);
        }

        $fileConfig = FileHelper::fresnsFileStorageConfigByType($fileType);

        if (! $fileConfig['antiLinkStatus']) {
            $digital = rand(72, 168);

            return now()->addHours($digital);
        }

        $cacheTime = now()->addMinutes($fileConfig['antiLinkExpire'] - 1);

        return $cacheTime;
    }

    /**
     * clear all cache.
     */
    public static function clearAllCache()
    {
        Cache::flush();
        \Artisan::call('view:cache');
        \Artisan::call('config:cache');
        \Artisan::call('event:cache');
    }

    /**
     * forget fresns config.
     */
    public static function forgetFresnsConfig()
    {
        Cache::forget('fresns_panel_path');
        Cache::forget('fresns_news');
        Cache::forget('fresns_current_version');
        Cache::forget('fresns_new_version');
        Cache::forget('fresns_database_timezone');
        Cache::forget('fresns_database_datetime');
        Cache::forget('fresns_crontab_items');
        Cache::forget('fresns_default_langTag');
        Cache::forget('fresns_default_timezone');
        Cache::forget('fresns_lang_tags');
        // Cache::forget("fresns_config_*");
        // Cache::forget("fresns_config_keys_*");
        // Cache::forget("fresns_config_tag_*");
        Cache::forget('fresns_content_block_words');
        Cache::forget('fresns_user_block_words');
        Cache::forget('fresns_dialog_block_words');
        Cache::forget('fresns_content_ban_words');
        Cache::forget('fresns_content_review_words');
        Cache::forget('fresns_user_ban_words');
        Cache::forget('fresns_dialog_ban_words');
    }

    /**
     * forget fresns multilingual info.
     */
    public static function forgetFresnsMultilingual(string $cacheName)
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        foreach ($langTagArr as $langTag) {
            $cacheKey = "{$cacheName}_{$langTag}";

            Cache::forget($cacheKey);
        }
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
    public static function forgetFresnsModel(string $modelName, string|int $fsid)
    {
        if (StrHelper::isPureInt($fsid)) {
            $model = PrimaryHelper::fresnsModelById($modelName, $fsid);

            $modelFsid = match ($modelName) {
                'config' => $model?->item_key,
                'account' => $model?->aid,
                'user' => $model?->uid,
                'group' => $model?->aid,
                'hashtag' => $model?->slug,
                'post' => $model?->pid,
                'comment' => $model?->cid,
                'file' => $model?->fid,
                'extend' => $model?->eid,

                default => null,
            };

            $fsidCacheKey = "fresns_model_{$modelName}_{$modelFsid}";
            $idCacheKey = "fresns_model_{$modelName}_{$fsid}";
        } else {
            $model = PrimaryHelper::fresnsModelByFsid($modelName, $fsid);

            $fsidCacheKey = "fresns_model_{$modelName}_{$fsid}";
            $idCacheKey = "fresns_model_{$modelName}_{$model?->id}";
        }

        if ($modelName == 'user') {
            Cache::forget("fresns_model_user_{$model?->uid}");
            Cache::forget("fresns_model_user_{$model?->username}");
        }

        Cache::forget($fsidCacheKey);
        Cache::forget($idCacheKey);
    }

    /**
     * forget table column lang content.
     *
     * fresns_{$tableName}_{$tableColumn}_{$tableId}_{$langTag}
     */
    public static function forgetFresnsTableColumnLangContent(string $tableName, string $tableColumn, int $tableId)
    {
        $cacheKey = "fresns_{$tableName}_{$tableColumn}_{$tableId}";

        CacheHelper::forgetFresnsMultilingual($cacheKey);
    }

    /**
     * forget fresns api multilingual info.
     *
     * fresns_api_auth_account_{$aid}_{$langTag}
     * fresns_api_archives_{$type}_{$unikey}_{$langTag}
     * fresns_api_stickers_{$langTag}
     */
    public static function forgetFresnsApiMultilingualInfo(string $cacheName)
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        foreach ($langTagArr as $langTag) {
            $cacheKey = "{$cacheName}_{$langTag}";

            Cache::forget($cacheKey);
        }
    }

    /**
     * forget fresns api multilingual and timezone info.
     *
     * fresns_api_publish_{$authUserId}_{$langTag}_{$timezone}
     */
    public static function forgetFresnsApiLangAndTimezoneInfo(string $cacheName)
    {
        $langTagArr = ConfigHelper::fresnsConfigLangTags();

        $langCacheKeyArr = null;
        foreach ($langTagArr as $langTag) {
            $cacheKey = "{$cacheName}_{$langTag}";

            $langCacheKeyArr[] = $cacheKey;
        }

        $utcArr = ConfigHelper::fresnsConfigByItemKey('utc');

        foreach ($langCacheKeyArr as $langCacheKey) {
            foreach ($utcArr as $utc) {
                $cacheKey = "{$langCacheKey}_{$utc}";

                Cache::forget($cacheKey);
            }
        }
    }

    /**
     * forget fresns api info.
     *
     * fresns_plugin_{$unikey}_url
     * fresns_plugin_{$unikey}_{$parameterKey}_url
     * fresns_user_follow_{$type}_{$authUserId}
     * fresns_user_block_{$type}_{$authUserId}
     * fresns_api_key_{$appId}
     * fresns_api_token_{$platformId}_{$aid}_{$uid}
     * fresns_api_guest_groups
     * fresns_api_private_groups
     */
    public static function forgetFresnsApiInfo(string $cacheKey)
    {
        Cache::forget($cacheKey);
    }

    // forget fresns api account
    public static function forgetApiAccount(?string $aid = null)
    {
        CacheHelper::forgetFresnsMultilingual("fresns_api_account_{$aid}");
        CacheHelper::forgetFresnsModel('account', $aid);
    }

    // forget fresns api user
    public static function forgetApiUser(?int $uid = null)
    {
        CacheHelper::forgetFresnsMultilingual("fresns_api_user_{$uid}");
        CacheHelper::forgetFresnsModel('user', $uid);
        Cache::forget("fresns_api_user_{$uid}_groups");
    }

    /**
     * forget fresns web.
     *
     * fresns_web_api_host
     * fresns_web_api_key
     * fresns_web_key_model
     * fresns_web_api_config_all_{$langTag}
     * fresns_web_db_config_{$itemKey}_{$langTag}
     * fresns_web_group_categories_{$langTag}
     * fresns_web_api_top_list_{$langTag}
     */
    public static function forgetFresnsWeb()
    {
        Cache::forget('fresns_web_api_host');
        Cache::forget('fresns_web_api_key');
        Cache::forget('fresns_web_key_model');
        CacheHelper::forgetFresnsMultilingual("fresns_web_api_config_all");
        CacheHelper::forgetFresnsMultilingual("fresns_web_group_categories");
        CacheHelper::forgetFresnsMultilingual("fresns_web_api_top_list");
    }
}
