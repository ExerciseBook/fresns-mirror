<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\FsDb\FresnsDialogMessages;

use App\Fresns\Api\Base\Resources\BaseAdminResource;
use App\Fresns\Api\Http\Base\FresnsBaseService;

/**
 * List resource config handle.
 */
class FsResource extends FresnsBaseService
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
            'is_enable' => boolval($this->is_enable),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Merger
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}
