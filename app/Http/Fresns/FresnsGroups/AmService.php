<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsGroups;

use App\Base\Services\BaseAdminService;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsConfigs\FresnsConfigService;
use App\Http\Fresns\FresnsLanguages\FresnsLanguages;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;
class AmService extends BaseAdminService
{
    public function __construct()
    {
        $this->config = new AmConfig();
        $this->model = new AmModel();
        $this->resource = AmResource::class;
        $this->resourceDetail = AmResourceDetail::class;
    }

    public function common()
    {
        $common = parent::common();
        $common['selectOption'] = AmConfig::TEST_SELECT_OPTION;
        $common['recommendOption'] = AmConfig::RECOMMEND_OPTION;
        $common['typeModel'] = AmConfig::TYPE_MODE;
        $common['typeFind'] = AmConfig::TYPE_FIND;
        $common['typeFollow'] = AmConfig::TYPE_FOLLOW;
        $common['publishPostOption'] = AmConfig::PUBLISH_POST;
        // 小组管理员common
        $common['memberOption'] = FresnsMembers::buildSelectTreeDataByNoRankNum('id', 'name', ['is_enable' => 1]);
        // 角色权限common
        $common['roleOption'] = FresnsMemberRoles::buildSelectTreeData('id', 'name', []);
        // 语言
        $languageArr = FresnsConfigService::getLanguageStatus();
        $common['language_status'] = $languageArr['language_status'];
        $common['default_language'] = $languageArr['default_language'];
        $common['multilingualoption'] = $languageArr['languagesOption'];
        // 一级小组common
        $common['groupOption'] = FresnsGroups::buildSelectTreeData('id', 'name', ['is_enable' => 1]);
        $common['oneGroupOption'] = FresnsGroups::staticBuildSelectOptions('id','name',['parent_id'=>NULL]);
        // 插件
        $common['plugOption'] = FresnsPlugin::staticBuildSelectOptions2('unikey', 'name', []);
        return $common;
    }

    public function hookUpdateAfter($id)
    {
        $this->model->hookUpdateAfter($id);
    }

    // 生成数据
    public function buildTreeData(&$itemArr, &$categoryArr)
    {
        // dd($itemArr);

        foreach ($itemArr as &$item) {
            // dd($item->toArray());
            // 语言名称(多语言名称和描述)
            $nameArr = self::getLangaugeArr(AmConfig::CFG_TABLE, AmConfig::FORM_FIELDS_MAP['name'], $item);
            $descriptionArr = self::getLangaugeArr(AmConfig::CFG_TABLE, AmConfig::FORM_FIELDS_MAP['description'],
                $item);
            $item['nameArr'] = $nameArr;
            $item['descriptionArr'] = $descriptionArr;
            $children = $item->children;
            // admin_members
            $admin_members_arr = explode(',', $item->admin_members);
            $allow_view_arr = $item->allow_view != null ? explode(',', $item->allow_view) : [];
            $allow_post_arr = $item->allow_post != null ? explode(',', $item->allow_post) : [];
            $allow_comment_arr = $item->allow_post != null ? explode(',', $item->allow_comment) : [];
            $item['admin_members_arr'] = $admin_members_arr;
            $item['allow_view_arr'] = $allow_view_arr;
            $item['allow_post_arr'] = $allow_post_arr;
            $item['allow_comment_arr'] = $allow_comment_arr;
            // 这里获取直接的children
            $directChildren = [];
            foreach ($children as $child) {
                if ($child->parent_id == $item->id) {
                    $directChildren[] = $child;
                }
            }

            $children = $directChildren;

            //            dd(CommonHelper::objectToArray($directChildren));
            //            dd(CommonHelper::objectToArray($children));
            $c = [];
            $c['key'] = $item->id;
            $c['value'] = $item->id;
            $c['name'] = $item->name;
            $c['title'] = $item->name;

            if ($children && count($children) > 0) {
                $this->buildTreeData($children, $c['children']);
            }

            $categoryArr[] = $c;
        }

    }

    public static function getLangaugeArr($table, $table_field, $item)
    {
        $languageArr = FresnsConfigService::getLanguageStatus();
        $multilingual = $languageArr['languagesOption'];
        // dd($multilingual);
        $nameArr = [];
        foreach ($multilingual as $v) {
            $input = [
                'table_name' => $table,
                'table_field' => $table_field,
                'table_id' => $item->id,
                'lang_tag' => $v['key']
            ];
            $name = FresnsLanguages::where($input)->first();
            $v['lang_content'] = $name['lang_content'] ?? "";
            $nameArr[] = $v;;
        }
        return $nameArr;
    }


}