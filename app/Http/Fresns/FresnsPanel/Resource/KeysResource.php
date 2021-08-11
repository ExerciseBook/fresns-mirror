<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPanel\Resource;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsConfigs\FresnsConfigs;
use App\Http\Fresns\FresnsClientKeys\FresnsClientKeysConfig;
class KeysResource extends BaseAdminResource
{

    public function toArray($request)
    {
        // form 字段
        $formMap = FresnsClientKeysConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        $platforms = FresnsConfigs::where("item_key", "platforms")->first(["item_value"]);
        // 平台配置数据
        $platforms = json_decode($platforms['item_value'], true);
        $platformName = "";
        foreach ($platforms as $p) {
            if ($this->platform_id == $p['id']) {
                $platformName = $p['name'];
            }
        }
        $typeName = $this->type == 1 ? "主程API" : "插件API";
        // dd($author);
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
            'platformName' => $platformName,
            'typeName' => $typeName,
        ];

        // 合并
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}

