<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsLanguages;

use App\Base\Services\BaseAdminService;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsService;

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

        return $common;
    }

    //截取标签
    public static function conversionLangTag($langTag)
    {
        if (strstr($langTag, 'zh-Hans') || strstr($langTag, 'zh-Hant')) {
            $tagArr = explode('-', $langTag);
            if (count($tagArr) == 3) {
                $areaCode = array_pop($tagArr);
                $langCode = str_replace("-$areaCode", '', $langTag);
            } else {
                $areaCode = null;
                $langCode = $langTag;
            }
        } else {
            $tagArr = explode('-', $langTag);
            if (count($tagArr) == 2) {
                $areaCode = array_pop($tagArr);
                $langCode = str_replace("-$areaCode", '', $langTag);
            } else {
                $areaCode = null;
                $langCode = $langTag;
            }
        }

        $data['area_code'] = $areaCode;
        $data['lang_code'] = $langCode;

        return $data;
    }

    //获取对应的多语言
    public static function getLanguageByTableId($table, $field, $tableId, $langTag = null)
    {
        $lang_content = FresnsLanguages::where('table_name', $table)->where('table_field', $field)->where('table_id',
        $tableId)->where('lang_tag', $langTag)->value('lang_content');
        if (empty($lang_content)) {
            $langTag = ApiLanguageHelper::getDefaultLanguage();
            $lang_content = FresnsLanguages::where('table_name', $table)->where('table_field', $field)->where('table_id',
            $tableId)->where('lang_tag', $langTag)->value('lang_content');
        }

        return $lang_content;
    }

    public static function getLanguageByConfigs($table, $field, $tableKey, $langTag)
    {
        $lang_content = FresnsLanguages::where('table_name', $table)->where('table_field', $field)->where('table_key',
            $tableKey)->where('lang_tag', $langTag)->value('lang_content');
        if (empty($lang_content)) {
            $langTag = ApiLanguageHelper::getDefaultLanguage();
            $lang_content = FresnsLanguages::where('table_name', $table)->where('table_field', $field)->where('table_key',
            $tableKey)->where('lang_tag', $langTag)->value('lang_content');
        }

        return $lang_content;
    }

    //插入到表数据
    public static function addLanguages($json, $tableName, $tableField, $tableId)
    {
        AmModel::where('table_name', $tableName)->where('table_field', $tableField)->where('table_id',
            $tableId)->delete();
        $langArr = json_decode($json, true);
        $itemArr = [];
        foreach ($langArr as $lang) {
            $item = [];
            $item['table_name'] = $tableName;
            $item['table_field'] = $tableField;
            $item['table_id'] = $tableId;
            $tag = FresnsLanguagesService::conversionLangTag($lang['key']);
            $langCode = $tag['lang_code'];
            $areaCode = $tag['area_code'];
            $item['lang_code'] = $langCode;
            $item['area_code'] = $areaCode ?? null;
            $item['lang_tag'] = $lang['key'];
            $item['lang_content'] = $lang['lang_content'] ?? null;
            $itemArr[] = $item;
        }
        AmModel::insert($itemArr);
    }

    //获取表数据
    public static function getLanguages($tableName, $tableField, $tableId)
    {
        $languageArr = FresnsConfigsService::getLanguageStatus();
        $languagesOption = $languageArr['languagesOption'];

        //查询对应的语言
        $langMap = FresnsLanguages::where('table_name', $tableName)
            ->where('table_field', $tableField)
            ->where('table_id', $tableId)
            ->pluck('lang_content', 'lang_tag')
            ->toArray();

        $languageArr = [];
        if ($langMap) {
            foreach ($languagesOption as $languages) {
                $it = [];
                $it['key'] = $languages['key'];
                $it['text'] = $languages['text'];
                $it['lang_content'] = $langMap[$languages['key']] ?? '';
                $languageArr[] = $it;
            }
        }

        return $languageArr;
    }
}
