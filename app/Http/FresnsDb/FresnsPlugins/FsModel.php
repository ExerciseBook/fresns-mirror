<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPlugins;

use App\Base\Models\BaseAdminModel;
use Illuminate\Support\Facades\DB;

class AmModel extends BaseAdminModel
{
    protected $table = AmConfig::CFG_TABLE;

    // Front-end form field mapping
    public function formFieldsMap()
    {
        return AmConfig::FORM_FIELDS_MAP;
    }

    // New search criteria
    public function getAddedSearchableFields()
    {
        return AmConfig::ADDED_SEARCHABLE_FIELDS;
    }

    // hook - after adding
    public function hookStoreAfter($id)
    {
    }

    // Get plugins based on scenarios
    public static function buildSelectOptionsByUnikey($scene): array
    {
        if (empty($scene)) {
            return [];
        }

        $opts = DB::table(AmConfig::CFG_TABLE)
            ->select('unikey AS key', 'name AS text')
            ->where('scene', 'LIKE', "%$scene%")
            ->where('deleted_at', null)
            ->get()->toArray();

        return $opts;
    }

    public function initOrderByFields()
    {
        $orderByFields = [
            // 'rank_num' => 'ASC',
            'id' => 'ASC',
            // 'updated_at' => 'DESC',
        ];

        return $orderByFields;
    }
}
