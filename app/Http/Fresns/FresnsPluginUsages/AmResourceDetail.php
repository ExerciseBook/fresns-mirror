<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPluginUsages;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsLanguages\FresnsLanguages;
use App\Http\Fresns\FresnsConfigs\FresnsConfigService;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;

class AmResourceDetail extends BaseAdminResource
{

    public function toArray($request)
    {
        // form 字段
        $formMap = AmConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        // 语言名称
        $languageArr = FresnsConfigService::getLanguageStatus();
        $multilingual = $languageArr['languagesOption'];
        // dd($multilingual);
        $nameArr = [];
        foreach ($multilingual as $v) {
            $input = [
                'table_name' => AmConfig::CFG_TABLE,
                'table_field' => AmConfig::FORM_FIELDS_MAP['name'],
                'table_id' => $this->id,
                'lang_tag' => $v['key']
            ];
            // dd($input);
            $name = FresnsLanguages::where($input)->first();
            $v['lang_content'] = $name['lang_content'] ?? "";
            $nameArr[] = $v;;
        }
        // 角色
        $user_rolesArr = [];
        $roleNames = "";
        if ($this->member_roles) {
            $user_rolesArr = explode(',', $this->member_roles);
            $roleInfo = FresnsMemberRoles::whereIn('id', $user_rolesArr)->pluck('name')->toArray();
            $roleNames = implode(',', $roleInfo);
        }

        // 应用场景
        $sceneArr = explode(',', $this->scene);
        $sceneNameArr = [];
        if ($sceneArr) {
            foreach (AmConfig::SCONE_OPTION as $v) {
                $arr = [];
                if (in_array($v['key'], $sceneArr)) {
                    $arr = $v['title'];
                    $sceneNameArr[] = $arr;
                }
            }
        }
        $sceneNames = implode(',', $sceneNameArr);
        // 默认字段
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
            'name' => $nameArr,
            'roleInfo' => $roleInfo,
            'roleNames' => $roleNames,
            'scene' => $sceneArr,
            // 'roleNames' => $roleNames,
            'userRolesArr' => $user_rolesArr,
            // 'sceneArr' => $sceneArr,
            'sceneNames' => $sceneNames,
        ];

        // 合并
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}

