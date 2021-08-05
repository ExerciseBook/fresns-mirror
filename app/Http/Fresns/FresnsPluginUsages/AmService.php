<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPluginUsages;

use App\Base\Services\BaseAdminService;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;
use App\Http\Fresns\FresnsRole\FresnsRole;
use App\Http\Fresns\FresnsConfigs\FresnsConfigs;
use App\Http\Fresns\FresnsConfigs\FresnsConfigService;
use App\Http\Fresns\FresnsGroups\FresnsGroups;

class AmService extends BaseAdminService
{
    protected $needCommon = false;

    public function __construct()
    {
        $this->model = new AmModel();
        $this->resource = AmResource::class;
        $this->resourceDetail = AmResourceDetail::class;
    }

    public function common()
    {
        $common = parent::common();
        $common['TableName'] = AmConfig::CFG_TABLE;
        $common['selectOption'] = AmConfig::TEST_SELECT_OPTION;
        // 场景
        $common['sceneOption'] = AmConfig::SCONE_OPTION;
        $common['typeOption'] = AmConfig::TYPE_OPTION;
        // 小组
        $common['groupOption'] = FresnsGroups::buildSelectTreeData('id', 'name', ['is_enable' => 1]);

        $common['isGroupAdminOption'] = AmCOnfig::IS_GROUP_ADMIN_OPTION;
        // 语言
        $languageArr = FresnsConfigService::getLanguageStatus();
        $common['language_status'] = $languageArr['language_status'];
        $common['default_language'] = $languageArr['default_language'];
        $common['multilingualoption'] = $languageArr['languagesOption'];
        // dd($common);
        // 角色Tips
        $common['roleUsersTips'] = AmConfig::ROLE_USERS_TIPS;
        $common['editerNumberTips'] = AmConfig::EDITER_NUMBER_TIPS;
        $common['isAdminTips'] = AmConfig::IS_ADMIN_TIPS;
        // 插件
        $common['plugOption'] = FresnsPlugin::staticBuildSelectOptions2('unikey', 'name', []);
        // 角色
        $common['roleOption'] = FresnsRole::buildSelectTreeData('id', 'name', []);

        // 数据服务商插件
        $common['restfulPlugin'] = FresnsPlugin::where('scene', 'like', "%restful%")->get([
            'unikey as key',
            'name as text'
        ]);
        // dd($common['restfulPlugin']);
        return $common;
    }

    // 获取后台设置默认语言code
    public static function getDefaultLanguage()
    {
        $lang_code = FresnsConfigs::where('item_key', AmConfig::LANG_SETTINGS)->first(['item_value']);
        if (!$lang_code) {
            return "";
        }
        // dd($lang_code);
        $lang_code_arr = json_decode($lang_code['item_value'], true);
        $default = FresnsConfigs::where('item_key', AmConfig::DEFAULT_LANGUAGE)->first(['item_value']);
        // dd($default['item_value']);

        // $default = json_decode($default['item_value'],true);
        $code = "";
        // dd($lang_code_arr);
        foreach ($lang_code_arr as $v) {
            // dd($v);
            if ($default['item_value'] == $v['langTag']) {
                $code = $v['langTag'];
            }
        }
        // dd($code);
        return $code;
    }

    public function hookUpdateAfter($id)
    {
        $this->model->hookUpdateAfter($id);
    }
}