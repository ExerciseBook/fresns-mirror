<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Helpers;

use App\Http\FresnsDb\FresnsConfigs\FresnsConfigs;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguages;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsagesService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ApiLanguageHelper
{
    public static function getLanguages($table, $table_field, $table_id)
    {
        // dd(1);
        if (! $table_id) {
            return '';
        }
        $langTag = ApiLanguageHelper::getLangTagByHeader();
        // $languageArr = TweetConfigService::getLanguageStatus();
        // $default_language = FresnsPluginUsagesService::getDefaultLanguage();
        // dd($default_language);
        // if(empty($langTag)){
        //     $langTag = $default_language;
        // }
        // 留空则输出默认语言内容，查询不到默认语言则输出第一条
        // dd($default_language);
        $input = [
            'lang_tag' => $langTag,
            'table_field' => $table_field,
            'table_id' => $table_id,
            'table_name' => $table,
        ];
        $name = FresnsLanguages::where($input)->first();
        if (! $name) {
            $input = [
                'table_field' => $table_field,
                'table_id' => $table_id,
                'table_name' => $table,
            ];
            $name = FresnsLanguages::where($input)->first();
        }

        return $name;
    }

    // table_key
    public static function getLanguagesByItemKey($table, $table_field, $table_key)
    {
        if (! $table_key) {
            return '';
        }
        $langTag = ApiLanguageHelper::getLangTagByHeader();
        // $languageArr = TweetConfigService::getLanguageStatus();
        // $default_language = FresnsPluginUsagesService::getDefaultLanguage();
        // if(empty($langTag)){
        //     $langTag = $default_language;
        // }
        // 留空则输出默认语言内容，查询不到默认语言则输出第一条
        // dd($default_language);
        // dump($langTag);
        $input = [
            'lang_tag' => $langTag,
            // 'table_field' => 'item_key',
            'table_key' => $table_key,
            'table_name' => $table,
        ];
        // dump($input);
        $name = FresnsLanguages::where($input)->first();
        if (! $name) {
            $input = [
                // 'table_field' => 'item_key',
                'table_key' => $table_key,
                'table_name' => $table,
            ];
            $name = FresnsLanguages::where($input)->first();
        }
        // dump($name);
        $content = $name['lang_content'] ?? '';

        return $content;
    }

    //获取默认语言
    public static function getDefaultLanguage()
    {
        $defaultLanguage = ApiConfigHelper::getConfigByItemKey(FresnsConfigsConfig::DEFAULT_LANGUAGE);
        if (empty($defaultLanguage)) {
            $defaultLanguage = FresnsConfigs::where('item_key', FresnsConfigsConfig::DEFAULT_LANGUAGE)->value('item_value');
        }

        return $defaultLanguage;
    }

    //获取langTag
    public static function getLangTagByHeader()
    {
        $langTagHeader = request()->header('langTag');
        $langTag = null;
        if (! empty($langTagHeader)) {
            //如果不为空则去查询是否存在该语言
            $langSetting = FresnsConfigs::where('item_key', FresnsConfigsConfig::LANG_SETTINGS)->value('item_value');
            if (! empty($langSetting)) {
                $langSettingArr = json_decode($langSetting, true);
                foreach ($langSettingArr as $v) {
                    if ($v['langTag'] == $langTagHeader) {
                        $langTag = $langTagHeader;
                    }
                }
            }
        }

        //如果没有传多语言或者查询不到则查询默认语言
        if (empty($langTag)) {
            $langTag = ApiLanguageHelper::getDefaultLanguage();
        }

        return $langTag;
    }

    //api接口使用
    public static function getDefaultLanguageByApi()
    {
        $defaultLanguage = FresnsConfigs::where('item_key', FresnsConfigsConfig::DEFAULT_LANGUAGE)->where('is_restful',
            1)->value('item_value');
        // $langSettings = TweetConfigs::where('item_key',TweetConfigsConfig::LANG_SETTINGS)->value('item_value');
        // $langSettingsArr = json_decode($langSettings,true);
        // $default = null;
        // foreach($langSettingsArr as $v){
        //     if($v['key'] == $defaultLanguage){
        //         $default = $v['langTag'];
        //     }
        // }

        return $defaultLanguage;
    }

    //通过key过去对应的语言标签
    public static function getDefaultLanguageByKey($key)
    {
        $langSettings = FresnsConfigs::where('item_key', FresnsConfigsConfig::LANG_SETTINGS)->value('item_value');
        $langSettingsArr = json_decode($langSettings, true);
        $default = null;
        foreach ($langSettingsArr as $v) {
            if ($v['langTag'] == $key) {
                $default = $v['langTag'];
            }
        }

        return $default;
    }

    //通过语言标签去查询对应的key
    public static function getDefaultLanguageByLangTag($langTag)
    {
        $langSettings = FresnsConfigs::where('item_key', FresnsConfigsConfig::LANG_SETTINGS)->value('item_value');
        $langSettingsArr = json_decode($langSettings, true);
        $default = null;
        foreach ($langSettingsArr as $v) {
            if ($v['langTag'] == $langTag) {
                $default = $v['langTag'];
            }
        }

        return $default;
    }

    // 获取多语言
    public static function getAllLanguages($table, $table_field, $table_id)
    {
        if (! $table_id) {
            return '';
        }

        // dd($default_language);
        $input = [
            'table_field' => $table_field,
            'table_id' => $table_id,
            'table_name' => $table,
        ];
        $info = FresnsLanguages::where($input)->get();

        return $info;
    }

    public static function getLangTag()
    {
        $isControlApi = request()->input('is_control_api');
        if ($isControlApi == 1) {
            $userId = Auth::id();
            $langTag = request()->input('lang', Cache::get('lang_tag_'.$userId));
        } else {
            $langTag = ApiLanguageHelper::getLangTagByHeader();
        }

        return $langTag;
    }
}
