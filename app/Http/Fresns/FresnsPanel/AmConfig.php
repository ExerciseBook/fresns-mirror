<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPanel;

use App\Base\Config\BaseConfig;


class AmConfig extends BaseConfig
{

    CONST PLUGINS_TYPE = 2;
    CONST ENABLE_FALSE = 0;

    CONST NOTICE_URL = 'https://api.fresns.cn/news.json';

    CONST PLUGIN_TYPE4 = 4;
    CONST PLUGIN_TYPE3 = 3;
    CONST PLUGIN_TYPE1 = 1;

    CONST BACKEND_PATH_NOT = [
        'login','dashboard','settings','keys','admins','websites','apps','plugins'
    ];

    CONST LANGUAGE_MAP = [
        'en' => 'English - English',
        'es' => 'Español - Spanish',
        'fr' => 'Français - French',
        'de' => 'Deutsch - German',
        'ja' => '日本語 - Japanese',
        'ko' => '한국어 - Korean',
        'ru' => 'Русский - Russian',
        'pt' => 'Português - Portuguese',
        'id' => 'Bahasa Indonesia - Indonesian',
        'hi' => 'हिन्दी - Hindi',
        'zh-Hans' => '简体中文 - Chinese (Simplified)',
        'zh-Hant' => '繁體中文 - Chinese (Traditional)',
    ];

}
