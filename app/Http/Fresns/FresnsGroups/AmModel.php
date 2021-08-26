<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsGroups;

use App\Base\Models\BaseAdminModel;
use App\Base\Models\BaseCategoryModel;
use App\Helpers\StrHelper;
use App\Http\Fresns\FresnsFiles\FresnsFiles;
use App\Http\Fresns\FresnsLanguages\AmModel as FresnsLanguagesModel;
use App\Http\Fresns\FresnsLanguages\FresnsLanguagesService;
use Illuminate\Support\Facades\DB;

class AmModel extends BaseCategoryModel
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
        $request = request();
        $nameArr = json_decode($request->input('name_arr'), true);
        $descriptionArr = json_decode($request->input('description_arr'), true);
        if ($nameArr) {
            self::insertLanguage($nameArr, AmConfig::CFG_TABLE, AmConfig::FORM_FIELDS_MAP['name'], $id);
        }
        if ($descriptionArr) {
            self::insertLanguage($descriptionArr, AmConfig::CFG_TABLE, AmConfig::FORM_FIELDS_MAP['description'], $id);
        }
    }

    // hook - 编辑之后
    public function hookUpdateAfter($id)
    {
        $request = request();
        // 文件表
        if (request()->input('file_id')) {
            FresnsFiles::where('id', request()->input('icon_file_id'))->update([
                'table_type' => 1,
                'table_name' => AmConfig::CFG_TABLE,
                'table_field' => 'id',
                'table_id' => $id,
            ]);
        }
        // 语言表
        $nameArr = json_decode($request->input('name_arr'), true);
        $descriptionArr = json_decode($request->input('description_arr'), true);
        FresnsLanguagesModel::where('table_name', AmConfig::CFG_TABLE)->where('table_id', $id)->delete();
        if ($nameArr) {
            self::insertLanguage($nameArr, AmConfig::CFG_TABLE, AmConfig::FORM_FIELDS_MAP['name'], $id);
        }
        if ($descriptionArr) {
            self::insertLanguage($descriptionArr, AmConfig::CFG_TABLE, AmConfig::FORM_FIELDS_MAP['description'], $id);
        }
    }

    public static function insertLanguage($itemArr, $table_name, $table_filed, $table_id)
    {
        $inputArr = [];
        foreach ($itemArr as $v) {
            if ($v['lang_content']) {
                DB::table($table_name)->where('id', $table_id)->update([$table_filed => $v['lang_content']]);
            }
            $item = [];
            $tagArr = FresnsLanguagesService::conversionLangTag($v['lang_code']);
            // $tagArr = explode('-',$v['lang_code']);
            // $areaCode = array_pop($tagArr);
            // $langCode = str_replace("-$areaCode",'',$v['lang_code']);
            $item['lang_code'] = $tagArr['lang_code'];
            $item['area_code'] = $tagArr['area_code'];
            $item['lang_tag'] = $v['lang_code'];
            $item['lang_content'] = $v['lang_content'];
            $item['table_field'] = $table_filed;
            $item['table_id'] = $table_id;
            $item['table_name'] = $table_name;
            // $item['alias_key'] = $v['nickname'];
            $inputArr[] = $item;
        }
        FresnsLanguagesModel::insert($inputArr);
    }

    // 搜索排序字段
    public function initOrderByFields()
    {
        $sortType = request()->input('sortType', '');
        $sortWay = request()->input('sortWay', 2);
        $sortWayType = $sortWay == 2 ? 'DESC' : 'ASC';
        switch ($sortType) {
            case 'view':
                $orderByFields = [
                    'view_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'like':
                $orderByFields = [
                    'like_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'follow':
                $orderByFields = [
                    'follow_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'shield':
                $orderByFields = [
                    'shield_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'post':
                $orderByFields = [
                    'post_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'essence':
                $orderByFields = [
                    'essence_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
            case 'time':
                $orderByFields = [
                    'created_at' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;

            default:
                $orderByFields = [
                    'rank_num' => 'ASC',
                    // 'updated_at'    => 'DESC',
                ];

                return $orderByFields;
                break;
        }
    }
}
