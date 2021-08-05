<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Member;

class AmConfig
{
    //修改成员资料参数
    const MEMBER_EDIT = [
        'mname' => 'name',
        'nickname' => 'nickname',
        'avatarFileId' => 'avatar_file_id',
        'avatarFileUrl' => 'avatar_file_url',
        'gender' => 'gender',
        'birthday' => 'birthday',
        'bio' => 'bio',
        'dialogLimit' => 'dialog_limit',
        'timezone' => 'timezone',
        'language' => 'language',
        'iosDeviceToken' => 'device_token_ios',
        'androidDeviceToken' => 'device_token_android',
    ];
}