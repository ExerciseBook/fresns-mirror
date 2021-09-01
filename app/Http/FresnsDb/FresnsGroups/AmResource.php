<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsGroups;

use App\Base\Resources\BaseAdminResource;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsService;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguages;

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
        // 语言名称
        // dd(123);
        $languageArr = FresnsConfigsService::getLanguageStatus();
        $multilingual = $languageArr['languagesOption'];
        // dd($multilingual);
        $nameArr = [];
        foreach ($multilingual as $v) {
            $input = [
                'table_name' => AmConfig::CFG_TABLE,
                'table_field' => AmConfig::FORM_FIELDS_MAP['name'],
                'table_id' => $this->id,
                'lang_tag' => $v['key'],
            ];
            // dd($input);
            $name = FresnsLanguages::where($input)->first();
            $v['lang_content'] = $name['lang_content'] ?? '';
            $nameArr[] = $v;
        }

        $descriptionArr = [];
        foreach ($multilingual as $v) {
            $input = [
                'table_name' => AmConfig::CFG_TABLE,
                'table_field' => AmConfig::FORM_FIELDS_MAP['description'],
                'table_id' => $this->id,
                'lang_tag' => $v['key'],
            ];
            // dd($input);
            $name = FresnsLanguages::where($input)->first();
            $v['lang_content'] = $name['lang_content'] ?? '';
            $descriptionArr[] = $v;
        }
        $permission_decode = json_decode($this->permission, true);
        $publish_post = $permission_decode['publish_post'] ?? 1;
        $publish_comment = $permission_decode['publish_comment'] ?? 1;
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
            'permission_decode' => json_decode($this->permission, true),
            'nameArr' => $nameArr,
            'descriptionArr' => $descriptionArr,
            'publish_post' => $publish_post,
            'publish_comment' => $publish_comment,
        ];

        // Merger
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}
