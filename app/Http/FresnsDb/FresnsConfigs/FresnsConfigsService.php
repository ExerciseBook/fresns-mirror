<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsConfigs;

use App\Http\FresnsApi\Helpers\ApiLanguageHelper;

class FresnsConfigsService extends AmService
{
    //是否启用多语言
    public static function getLanguageStatus()
    {
        //是否启用
        $languageStatus = AmModel::where('item_key', AmConfig::LANGUAGE_STATUS)->value('item_value');
        // $languageStatusArr = json_decode($languageStatus,true);
        $common['language_status'] = $languageStatus ?? false;
        //默认
        $defaultLanguage = AmModel::where('item_key', AmConfig::DEFAULT_LANGUAGE)->value('item_value');
        // $defaultLanguageArr = json_decode($defaultLanguage,true);

        $common['default_language'] = ApiLanguageHelper::getDefaultLanguageByLangTag($defaultLanguage);
        $common['default_language_tag'] = $defaultLanguage ?? null;

        //多语言下拉框
        $langSettings = AmModel::where('item_key', AmConfig::LANG_SETTINGS)->first();
        $oldLangSettingJson = $langSettings['item_value'];
        $oldLangSettingArr = json_decode($oldLangSettingJson, true);
        $languageOptionArr = $oldLangSettingArr;

        if ($common['language_status'] == false) {
            $languageOptionArr = [];
            foreach ($oldLangSettingArr as $v) {
                if ($v['langTag'] == $common['default_language']) {
                    $languageOptionArr[] = $v;
                }
            }
        }

        $optionArr = [];
        foreach ($languageOptionArr as $v) {
            $item = [];
            $item['key'] = $v['langTag'];
            if ($v['areaCode']) {
                $item['text'] = $v['langName'].'('.$v['areaName'].')';
            } else {
                $item['text'] = $v['langName'];
            }
            $optionArr[] = $item;
        }

        $common['languagesOption'] = $optionArr;

        return $common;
    }

    public static function addLikeCounts($key)
    {
        $item = AmModel::where('item_key', $key)->first();
        if (empty($item)) {
            $input = [
                'item_key' => $key,
                'item_value' => 1,
                'item_tag' => 'stats',
            ];
            AmModel::insert($input);
        } else {
            AmModel::where('item_key', $key)->increment('item_value');
        }
    }

    public static function delLikeCounts($key)
    {
        AmModel::where('item_key', $key)->decrement('item_value');
    }
}
