<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsLanguages;

use App\Base\Services\BaseAdminService;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsService;

class FsService extends BaseAdminService
{
    public function __construct()
    {
        $this->model = new FsModel();
        $this->resource = FsResource::class;
        $this->resourceDetail = FsResourceDetail::class;
    }

    public function common()
    {
        $common = parent::common();
        return $common;
    }

    // Intercepting tags
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

    // Get the corresponding multilingual
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

    public static function getLanguageByTableKey($table, $field, $tableKey, $langTag)
    {
        $lang_content = FresnsLanguages::where('table_name', $table)->where('table_field', $field)->where('table_key', $tableKey)->where('lang_tag', $langTag)->value('lang_content');
        if (empty($lang_content)) {
            $langTag = ApiLanguageHelper::getDefaultLanguage();
            $lang_content = FresnsLanguages::where('table_name', $table)->where('table_field', $field)->where('table_key',
            $tableKey)->where('lang_tag', $langTag)->value('lang_content');
        }

        return $lang_content;
    }

    // Insert into table data
    public static function addLanguages($json, $tableName, $tableField, $tableId)
    {
        FsModel::where('table_name', $tableName)->where('table_field', $tableField)->where('table_id',
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
        FsModel::insert($itemArr);
    }

    // Get table data
    public static function getLanguages($tableName, $tableField, $tableId)
    {
        $languageArr = FresnsConfigsService::getLanguageStatus();
        $languagesOption = $languageArr['languagesOption'];

        // Search for the corresponding language
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
