<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsPanel;

use App\Base\Services\BaseAdminService;

class AmService extends BaseAdminService
{
    // Get the current setting language
    public static function getLanguage($lang)
    {
        $map = AmConfig::LANGUAGE_MAP;
        return $map[$lang] ?? 'English - English';
    }
}
