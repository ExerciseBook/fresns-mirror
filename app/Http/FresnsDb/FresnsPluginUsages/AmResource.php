<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPluginUsages;

use App\Base\Resources\BaseAdminResource;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsService;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguages;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins;

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
        // 插件名称
        $plugInfo = FresnsPlugins::where('unikey', $this->plugin_unikey)->first();
        // 语言名称
        // 获取默认语言code
        $defaultCode = AmService::getDefaultLanguage();
        $lang_code = request()->header('lang-code', $defaultCode);
        foreach (AmConfig::MULTILINGUAL_OPTION as $v) {
            if ($v['nickname'] == $lang_code) {
                $lang_code = $v['key'];
            }
        }
        $input = [
            'table_name' => AmConfig::CFG_TABLE,
            'table_field' => AmConfig::FORM_FIELDS_MAP['name'],
            'table_id' => $this->id,
            'lang_tag' => $lang_code,
        ];
        $names = FresnsLanguages::where($input)->first();
        if (! $names) {
            $input = [
                'table_name' => AmConfig::CFG_TABLE,
                'table_field' => AmConfig::FORM_FIELDS_MAP['name'],
                'table_id' => $this->id,
            ];
            $names = FresnsLanguages::where($input)->first();
        }
        // 语言名称

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
        // dd($nameArr);
        // 角色
        $user_rolesArr = explode(',', $this->member_roles);
        $roleInfo = FresnsMemberRoles::whereIn('id', $user_rolesArr)->pluck('name')->toArray();
        $roleNames = implode(',', $roleInfo);
        // $user_roles_arr = explode(',',$this->user_roles);
        // 应用场景
        $sceneArr = explode(',', $this->scene);
        // dd($sceneArr);
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
        $parameter = json_decode($this->parameter, true);
        $sort_number = json_decode($this->data_sources, true);

        $newArr = [];
        // sort_number参数过滤
        if (! $sort_number) {
            $arr = [];
            foreach ($multilingual as &$m) {
                $arr['id'] = '';
                $intro = [];
                $intro['lang_code'] = $m['key'];
                $intro['text'] = $m['text'];
                $intro['title'] = '';
                $intro['description'] = '';
                $arr['intro'] = $intro;
                $newArr['postLists'][] = $arr;
                $newArr['postFollows'][] = $arr;
                $newArr['postNearbys'][] = $arr;
            }
        } else {
            // sort_number参数过滤
            $arr1 = [];
            foreach ($sort_number as $k => &$s) {
                // $sArr = [];
                foreach ($s as &$v) {
                    $introArr = [];
                    foreach ($v['intro'] as $i) {
                        $map[$i['lang_code']] = $i;
                    }
                    foreach ($multilingual as $m) {
                        $item = [];
                        $item['title'] = $map[$m['key']]['title'] ?? '';
                        $item['lang_code'] = $m['key'];
                        $item['text'] = $m['text'];
                        $item['description'] = $map[$m['key']]['description'] ?? '';
                        $introArr[] = $item;
                    }

                    // $item1['intro']= $introArr;
                    $v['intro'] = $introArr;
                    // $sArr[] = $item1;
                }
                // $arr1[$k] = $sArr;
            }
            // dd($sort_number);
            $newArr = $sort_number;
        }
        // dd($multilingual);
        // dd($newArr);
        // 数据来源
        $source_parameter = AmConfig::SOURCE_PARAMETER;
        foreach ($source_parameter as &$v) {
            $v['postLists'] = $parameter[$v['nickname']] ?? '';
            $v['sort_number'] = $newArr[$v['nickname']] ?? '';
        }
        // dd($source_parameter);
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
            'plug_name' => $plugInfo['name'] ?? '',
            'name' => $names['lang_content'] ?? '',
            'roleNames' => $roleNames,
            'roleNamesArr' => $roleInfo,
            'userRolesArr' => $user_rolesArr,
            'scene' => $sceneArr,
            // 'sceneArr' => $sceneArr,
            'sceneNames' => $sceneNames,
            'nameArr' => $nameArr,
            'source_parameter' => $source_parameter,
            'sort_number' => json_decode($this->sort_number, true),
            // 'is_group_admin' => boolval($this->is_group_admin)
        ];

        // Merger
        $arr = array_merge($formMapFieldsArr, $default);

        return $arr;
    }
}
