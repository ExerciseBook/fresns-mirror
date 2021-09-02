<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPlugins;

use App\Base\Resources\BaseAdminResource;
use App\Http\Center\Helper\PluginHelper;

/**
 * List resource config processing
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
        // 是否有新版本
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
