<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsDomains;

use App\Base\Resources\BaseAdminResource;

class AmResourceDetail extends BaseAdminResource
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
            'key' => $this->id,
            'id' => $this->id,
            'is_enable' => boolval($this->is_enable),
            'disabled' => false,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'nickname' => $this->nickname,
            'more_json' => $this->more_json,
            'more_json_decode' => json_decode($this->more_json, true),
        ];

        // Merger
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}
