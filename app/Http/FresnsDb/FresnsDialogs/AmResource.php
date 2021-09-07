<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsDialogs;

use App\Base\Resources\BaseAdminResource;

/**
 * List resource config handle
 */

class AmResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // Form Field
        $formMap = AmConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }

        // Default Field
        $default = [
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];

        // Merger
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}
