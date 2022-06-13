<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Traits;

use App\Helpers\ConfigHelper;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

trait ApiHeaderTrait
{
    // platformId
    public function platformId(): int
    {
        return \request()->header('platformId');
    }

    // version
    public function version(): string
    {
        return \request()->header('version');
    }

    // appId
    public function appId(): string
    {
        return \request()->header('appId');
    }

    // langTag
    public function langTag(): string
    {
        $defaultLanguage = ConfigHelper::fresnsConfigDefaultLangTag();

        return \request()->header('langTag', $defaultLanguage);
    }

    // timezone
    public function timezone(): string
    {
        $defaultTimezone = ConfigHelper::fresnsConfigDefaultTimezone();

        return \request()->header('timezone', $defaultTimezone);
    }

    // auth account
    public function account(): ?Account
    {
        $aid = \request()->header('aid');

        if (empty($aid)) {
            return null;
        }

        $langTag = $this->langTag();

        $cacheKey = 'fresns_api_account_'.$aid.'_'.$langTag;

        $authAccount = Cache::rememberForever($cacheKey, function () use ($aid) {
            return Account::withTrashed()->where('aid', $aid)->first();
        });

        if (is_null($authAccount)) {
            Cache::forget($cacheKey);
        }

        return $authAccount;
    }

    // forget account cache
    public function forgetAccountCache()
    {
        $aid = \request()->header('aid');

        if (empty($aid)) {
            return;
        }

        $langTag = $this->langTag();

        $cacheKey = 'fresns_api_account_'.$aid.'_'.$langTag;

        Cache::forget($cacheKey);

        return;
    }

    // auth user
    public function user(): ?User
    {
        $uid = \request()->header('uid');

        if (empty($uid)) {
            return null;
        }

        $langTag = $this->langTag();

        $cacheKey = 'fresns_api_user_'.$uid.'_'.$langTag;

        $authUser = Cache::remember($cacheKey, now()->addHours(1), function () use ($uid) {
            return User::withTrashed()->where('uid', $uid)->first();
        });

        if (is_null($authUser)) {
            Cache::forget($cacheKey);
        }

        return $authUser;
    }

    // forget user cache
    public function forgetUserCache()
    {
        $uid = \request()->header('uid');

        if (empty($uid)) {
            return;
        }

        $langTag = $this->langTag();

        $cacheKey = 'fresns_api_user_'.$uid.'_'.$langTag;

        Cache::forget($cacheKey);

        return;
    }
}
