<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsExtends;

use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // Main Table
    const CFG_TABLE = 'extends';

    // Additional search columns in the main table
    const ADDED_SEARCHABLE_FIELDS = [
        'searchEid' => ['field' => 'content', 'op' => 'LIKE'],
        'searchType' => ['field' => 'extend_type', 'op' => 'LIKE'],
        'searchKey' => ['field' => 'title', 'op' => 'LIKE'],
        'searchMid' => ['field' => 'member_id', 'op' => 'LIKE'],
    ];

    // Model Usage - Form Mapping
    const FORM_FIELDS_MAP = [
        'id' => 'id',
    ];
}
