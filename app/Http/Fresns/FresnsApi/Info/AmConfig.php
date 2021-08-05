<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Info;

class AmConfig
{
    const TEAMPLATE_1 = 1;
    const TEAMPLATE_2 = 2;
    const TEAMPLATE_3 = 3;
    const TEAMPLATE_4 = 4;
    const TEAMPLATE_7 = 7;
    const COUNTRYCODE = 86;
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

    //需要更新的configs表item_key
    const CONFIGS_ITEM_KEY = [
        'platforms',
        'language_codes',
        'language_pack',
        'connects',
        'disable_names',
        'utc',
        'continents',
        'area_codes',
        'currency_codes',
        'storages',
        'maps',
        'default_language',
        'language_status',
        'languages'
    ];
}