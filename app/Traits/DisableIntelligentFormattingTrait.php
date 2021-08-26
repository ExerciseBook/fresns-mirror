<?php
/*
 * 禁用Laravel Excel智能格式化，避免科学计数
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

//namespace Maatwebsite\Excel\Concerns;

namespace App\Traits;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;

trait DisableIntelligentFormattingTrait
{
    public function bindValue(Cell $cell, $value)
    {
        // sanitize UTF-8 strings
        if (is_string($value)) {
            $value = StringHelper::sanitizeUTF8($value);
        }

        // Set value explicit
        if ($cell->getRow() > 1) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
        }

        // Done!
        return true;
    }
}
