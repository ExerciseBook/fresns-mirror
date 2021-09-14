<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPlugins;

use App\Base\Resources\BaseAdminResource;
use App\Http\Center\Helper\PluginHelper;

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
        // Is there a new version
        $isNewVision = AmConfig::NO_NEWVISION;
        $newVisionInt = '';
        $newVision = '';
        $author = '';

        // Default Field
        $default = [
            'id' => $this->id,
            'author' => $author,
            'isNewVision' => $isNewVision,
            'newVision' => $newVision,
            'newVisionInt' => $newVisionInt,
            'isDownload' => $isDownload,
            'downloadUrl' => $downloadUrl,
            'more_json' => $this->more_json,
            'more_json_decode' => json_decode($this->more_json, true),
            'is_enable' => boolval($this->is_enable),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Merger
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}
