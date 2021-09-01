<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsMemberLikes;

use App\Base\Models\BaseAdminModel;

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

    // Search for sorted fields
    public function initOrderByFields()
    {
        $sortWay = request()->input('sortWay', 2);
        $sortWayType = $sortWay == 2 ? 'DESC' : 'ASC';
        $orderByFields = [
            'created_at' => $sortWayType,
            // 'updated_at'    => 'DESC',
        ];

        return $orderByFields;
    }
}
