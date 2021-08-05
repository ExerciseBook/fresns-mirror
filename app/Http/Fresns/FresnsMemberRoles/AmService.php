<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsMemberRoles;

use App\Base\Services\BaseAdminService;
use App\Plugins\Tweet\TweetConfigs\TweetConfigService;

class AmService extends BaseAdminService
{
    public function __construct()
    {
        $this->model = new AmModel();
        $this->resource = AmResource::class;
        $this->resourceDetail = AmResourceDetail::class;
    }

    public function common()
    {
        $common = parent::common();
        $common['selectOption'] = AmConfig::TEST_SELECT_OPTION;
        $common['typeOption'] = AmConfig::TYPE_OPTION;
        // 语言
        $languageArr = TweetConfigService::getLanguageStatus();
        $common['languagesOption'] = $languageArr['languagesOption'];
        $option = AmModel::staticBuildSelectOptions('id', 'name');
        $common['memberOption'] = $option;
        return $common;
    }

    public function update($id)
    {
        parent::update($id);
        $this->model->hookUpdateAfter($id);
    }

    //获取权限的map
    public static function getPermissionMap($permissionArr)
    {
        $permissionMap = [];
        foreach ($permissionArr as $v) {
            if (empty($v['permKey']) || !isset($v['permValue'])) {
                return [];
                break;
            }
            $permissionMap[$v['permKey']] = $v['permValue'];
        }

        return $permissionMap;
    }

}