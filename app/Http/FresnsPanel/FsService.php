<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsPanel;

use App\Base\Services\BaseAdminService;

class FsService extends BaseAdminService
{
    // Get the current setting language
    public static function getLanguage($lang)
    {
        $map = FsConfig::LANGUAGE_MAP;
        return $map[$lang] ?? 'English - English';
    }
}
