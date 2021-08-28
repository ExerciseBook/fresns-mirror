<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Notify;

use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 消息表类型
    const SOURCE_TYPE_1 = 1;
    const SOURCE_TYPE_2 = 2;
    const SOURCE_TYPE_3 = 3;
    const SOURCE_TYPE_4 = 4;
    const SOURCE_TYPE_5 = 5;
    const SOURCE_TYPE_6 = 6;
    // 阅读状态
    const NO_READ = 1;
    const READED = 2;
    const DEFAULT_AVATAR = 'default_avatar';
    const ANONYMOUS_AVATAR = 'anonymous_avatar';
    const DEACTIVATE_AVATAR = 'deactivate_avatar';
    const DIALOG_STATUS = 'dialog_status';
    const SITE_MODEL = 'site_mode';
    const PRIVATE = 'private';
    const OBJECT_SUCCESS = 2;
}
