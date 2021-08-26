<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPanel;

use App\Base\Services\BaseAdminService;

class AmService extends BaseAdminService
{
    //获取当前设置语言
    public static function getLanguage($lang)
    {
        $map = AmConfig::LANGUAGE_MAP;

        return $map[$lang] ?? 'English - English';
    }
}
