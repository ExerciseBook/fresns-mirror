<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsMembers;

use App\Base\Resources\BaseAdminResource;

/**
 * List resource config handle
 */

class FsResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // Form Field
        $formMap = FsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }

        // Default Field
        $default = [
            'id' => $this->id,
            'mid' => $this->uuid,
            'mname' => $this->name,
            'nickname' => $this->nickname,
            'is_enable' => boolval($this->is_enable),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Merger
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}
