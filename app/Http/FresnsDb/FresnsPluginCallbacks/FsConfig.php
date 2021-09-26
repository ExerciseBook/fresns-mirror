<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPluginCallbacks;

use App\Base\Config\BaseConfig;

class FsConfig extends BaseConfig
{
    // Main Table
    const CFG_TABLE = 'plugin_callbacks';

    // Additional search columns in the main table
    const ADDED_SEARCHABLE_FIELDS = [

    ];

    const ENABLE_FALSE = 0;
    const ENABLE_TRUE = 1;

    // Plugin download or not
    const NO_DOWNLOAD = 0;
    const DOWNLOAD = 1;
    
    // Is the new version
    const NO_NEWVISION = 0;
    const NEWVISION = 1;

    // Model Usage - Form Mapping
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'plugin_unikey' => 'plugin_unikey',
        'member_id' => 'member_id',
        'uuid' => 'uuid',
        'types' => 'types',
        'content' => 'content',
        'status' => 'status',
        'use_plugin_unikey' => 'use_plugin_unikey',
    ];
}
