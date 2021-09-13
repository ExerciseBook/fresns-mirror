<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsImplants;

use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // Main Table
    const CFG_TABLE = 'implants';

    // Additional search columns in the main table
    const ADDED_SEARCHABLE_FIELDS = [

    ];

    // Model Usage - Form Mapping
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'implant_type' => 'implant_type',
        'implant_id' => 'implant_id',
        'implant_template' => 'implant_template',
        'implant_name' => 'implant_name',
        'plugin_unikey' => 'plugin_unikey',
        'type' => 'type',
        'target' => 'target',
        'value' => 'value',
        'support' => 'support',
        'position' => 'position',
        'starting_at' => 'starting_at',
        'expired_at' => 'expired_at',
    ];
}
