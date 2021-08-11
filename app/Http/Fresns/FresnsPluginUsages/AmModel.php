<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPluginUsages;

use App\Base\Models\BaseAdminModel;
use App\Http\Fresns\FresnsFiles\FresnsFiles;
use App\Http\Fresns\FresnsLanguages\FresnsLanguagesService;
use App\Http\Fresns\FresnsLanguages\AmModel as FresnsLanguagesModel;

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
        // 文件表
        if (request()->input('icon_file_id')) {
            FresnsFiles::where('id', request()->input('icon_file_id'))->update([
                'table_type' => 1,
                'table_name' => AmConfig::CFG_TABLE,
                'table_field' => 'id',
                'table_id' => $id
            ]);
        }

        // 语言表
        $nameArr = json_decode(request()->input('name'), true);
        $inputArr = [];
        foreach ($nameArr as $v) {

            $item = [];
            $tagArr = FresnsLanguagesService::conversionLangTag($v['lang_code']);
            // $tagArr = explode('-',$v['lang_code']);
            // $areaCode = array_pop($tagArr);
            // $langCode = str_replace("-$areaCode",'',$v['lang_code']);
            $item['lang_code'] = $tagArr['lang_code'];
            $item['area_code'] = $tagArr['area_code'];
            $item['lang_tag'] = $v['lang_code'];
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
                'table_id' => $id
            ]);
        }
        // 语言表
        $nameArr = json_decode(request()->input('name'), true);
        $inputArr = [];
        FresnsLanguagesModel::where('table_name', AmConfig::CFG_TABLE)->where('table_field',
            AmConfig::FORM_FIELDS_MAP['name'])->where('table_id', $id)->delete();
        foreach ($nameArr as $v) {
            $item = [];
            $tagArr = FresnsLanguagesService::conversionLangTag($v['lang_code']);
            // $tagArr = explode('-',$v['lang_code']);
            // $areaCode = array_pop($tagArr);
            // $langCode = str_replace("-$areaCode",'',$v['lang_code']);
            $item['lang_code'] = $tagArr['lang_code'];
            $item['area_code'] = $tagArr['area_code'];
            $item['lang_tag'] = $v['lang_code'];
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

