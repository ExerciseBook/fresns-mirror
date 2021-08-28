<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPlugins;

use App\Base\Resources\BaseAdminResource;
use App\Http\Center\Helper\PluginHelper;

class AmResource extends BaseAdminResource
{
    public function toArray($request)
    {
        // form 字段
        $formMap = AmConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        // 插件是否下载
        $localPlugin = PluginHelper::getPluginJsonFileArr();
        // 是否下载
        $isDownload = AmConfig::NO_DOWNLOAD;

        if ($localPlugin) {
            foreach ($localPlugin as $plugin) {
                if ($this->unikey == $plugin['uniKey']) {
                    $isDownload = AmConfig::DOWNLOAD;
                }
            }
        }
        // 是否有新版本
        $isNewVision = AmConfig::NO_NEWVISION;
        $newVisionInt = '';
        $newVision = '';
        $author = '';
        
        // 默认字段
        $default = [
            'key' => $this->id,
            'id' => $this->id,
            'is_enable' => boolval($this->is_enable),
            'disabled' => false,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'more_json' => $this->more_json,
            'more_json_decode' => json_decode($this->more_json, true),
            'isDownload' => $isDownload,
            'isNewVision' => $isNewVision,
            'newVisionInt' => $newVisionInt,
            'newVision' => $newVision,
            'downloadUrl' => $downloadUrl,
            'author' => $author,
        ];

        // 合并
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}
