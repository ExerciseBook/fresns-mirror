<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsDialogMessages;

use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // Main Table
    const CFG_TABLE = 'dialog_messages';

    // Additional search columns in the main table
    const ADDED_SEARCHABLE_FIELDS = [
        'dialog_id' => ['field' => 'dialog_id', 'op' => '='],
        'ids' => ['field' => 'id', 'op' => 'in'],
    ];

    // Model Usage - Form Mapping
    const FORM_FIELDS_MAP = [
        'id' => 'id',
    ];
}
