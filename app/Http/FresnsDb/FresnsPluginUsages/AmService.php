<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPluginUsages;

use App\Base\Services\BaseAdminService;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsService;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins;

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

        // Scenes
        $common['sceneOption'] = AmConfig::SCONE_OPTION;
        $common['typeOption'] = AmConfig::TYPE_OPTION;

        // Group
        $common['groupOption'] = FresnsGroups::buildSelectTreeData('id', 'name', ['is_enable' => 1]);
        $common['isGroupAdminOption'] = AmCOnfig::IS_GROUP_ADMIN_OPTION;

        // Languages
        $languageArr = FresnsConfigsService::getLanguageStatus();
        $common['language_status'] = $languageArr['language_status'];
        $common['default_language'] = $languageArr['default_language'];
        $common['multilingualoption'] = $languageArr['languagesOption'];

        // Tips
        $common['roleMembersTips'] = AmConfig::ROLE_MEMBERS_TIPS;
        $common['editerNumberTips'] = AmConfig::EDITER_NUMBER_TIPS;
        $common['isAdminTips'] = AmConfig::IS_ADMIN_TIPS;

        // Plugin
        $common['plugOption'] = FresnsPlugins::staticBuildSelectOptions2('unikey', 'name', []);
        
        // Role
        $common['roleOption'] = FresnsMemberRoles::buildSelectTreeData('id', 'name', []);

        // Data Service Provider Plugin
        $common['restfulPlugin'] = FresnsPlugins::where('scene', 'like', '%restful%')->get([
            'unikey as key',
            'name as text',
        ]);
        return $common;
    }

    // Get default language tag
    public static function getDefaultLanguage()
    {
        $languageArr = FresnsConfigsService::getLanguageStatus();
        $code = $languageArr['default_language'];
        
        return $code;
    }

    public function hookUpdateAfter($id)
    {
        $this->model->hookUpdateAfter($id);
    }
}
