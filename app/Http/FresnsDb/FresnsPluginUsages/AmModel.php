<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPluginUsages;

use App\Base\Models\BaseAdminModel;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsLanguages\AmModel as FresnsLanguagesModel;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguagesService;

class AmModel extends BaseAdminModel
{
    protected $table = AmConfig::CFG_TABLE;

    // Front-end form field mapping
    public function formFieldsMap()
    {
        return AmConfig::FORM_FIELDS_MAP;
    }

    // New search criteria
    public function getAddedSearchableFields()
    {
        return AmConfig::ADDED_SEARCHABLE_FIELDS;
    }

    // hook - after adding
    public function hookStoreAfter($id)
    {
        // 文件表
        if (request()->input('icon_file_id')) {
            FresnsFiles::where('id', request()->input('icon_file_id'))->update([
                'table_type' => 1,
                'table_name' => AmConfig::CFG_TABLE,
                'table_field' => 'id',
                'table_id' => $id,
            ]);
        }

        // 语言表
        $nameArr = json_decode(request()->input('name'), true);
        $inputArr = [];
        foreach ($nameArr as $v) {
            $item = [];
            $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
            $item['lang_code'] = $tagArr['lang_code'];
            $item['area_code'] = $tagArr['area_code'];
            $item['lang_tag'] = $v['langTag'];
            $item['lang_content'] = $v['lang_content'];
            $item['table_field'] = AmConfig::FORM_FIELDS_MAP['name'];
            $item['table_id'] = $id;
            $item['table_name'] = AmConfig::CFG_TABLE;
            // $item['alias_key'] = $v['nickname'];
            $inputArr[] = $item;
        }
        FresnsLanguagesModel::insert($inputArr);
    }

    // hook - 编辑之后
    public function hookUpdateAfter($id)
    {
        // 文件表
        if (request()->input('icon_file_id')) {
            FresnsFiles::where('id', request()->input('icon_file_id'))->update([
                'table_type' => 1,
                'table_name' => AmConfig::CFG_TABLE,
                'table_field' => 'id',
                'table_id' => $id,
            ]);
        }
        // 语言表
        $nameArr = json_decode(request()->input('name'), true);
        $inputArr = [];
        FresnsLanguagesModel::where('table_name', AmConfig::CFG_TABLE)->where('table_field',
            AmConfig::FORM_FIELDS_MAP['name'])->where('table_id', $id)->delete();
        foreach ($nameArr as $v) {
            $item = [];
            $tagArr = FresnsLanguagesService::conversionLangTag($v['langTag']);
            $item['lang_code'] = $tagArr['lang_code'];
            $item['area_code'] = $tagArr['area_code'];
            $item['lang_tag'] = $v['langTag'];
            $item['lang_content'] = $v['lang_content'];
            $item['table_field'] = AmConfig::FORM_FIELDS_MAP['name'];
            $item['table_id'] = $id;
            $item['table_name'] = AmConfig::CFG_TABLE;
            // $item['alias_key'] = $v['nickname'];
            $inputArr[] = $item;
        }
        FresnsLanguagesModel::insert($inputArr);
    }
}
