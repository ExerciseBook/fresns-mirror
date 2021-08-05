<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsEmojis;

use App\Base\Models\BaseAdminModel;
use App\Plugins\Tweet\TweetLanguages\TweetLanguagesService;

class AmModel extends BaseAdminModel
{
    protected $table = AmConfig::CFG_TABLE;

    // 前台表单字段映射
    public function formFieldsMap()
    {
        return AmConfig::FORM_FIELDS_MAP;
    }

    // 新增搜索条件
    public function getAddedSearchableFields()
    {
        return AmConfig::ADDED_SEARCHABLE_FIELDS;
    }

    // hook-添加之后
    public function hookStoreAfter($id)
    {
        $langJson = request()->input('lang_json');
        if ($langJson) {
            TweetLanguagesService::addLanguages($langJson, AmConfig::CFG_TABLE, 'name', $id);

        }
    }

    public function hookUpdateAfter($id)
    {
        $langJson = request()->input('lang_json');
        if ($langJson) {
            TweetLanguagesService::addLanguages($langJson, AmConfig::CFG_TABLE, 'name', $id);
        }
    }

}

