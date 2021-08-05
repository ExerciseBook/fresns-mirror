<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Plugins\Tweet\TweetLanguages\TweetLanguagesService;
use Illuminate\Database\Connectors\ConnectionFactory;
use Illuminate\Support\Facades\DB;

class FresnsConfig
{
    public static function configSet($itemKey, $columnName,$columnValue){
        $t = date("Y-m-d H:i:s", time());

        //echo env("APP_NAME"); exit;

        $configItem =  DB::connection('mysql')->table('configs')->where('item_key', $itemKey)->first();

        if(empty($configItem)){
            $upInfo = [
                'item_key'   => $itemKey,
                $columnName => $columnValue,
            ];
            DB::table('configs')->where('item_key', $itemKey)->insert($upInfo);
        }else{
            $upInfo = [
                $columnName   => $columnValue,
                'updated_at'   => date("Y-m-d H:i:s", time())
            ];
            DB::table('configs')->where('item_key', $itemKey)->update($upInfo);
        }

        return true;
    }

    public static function configLangSet($itemKey,$content,$lang){
        $t = date("Y-m-d H:i:s", time());

        $languagesItem =  DB::connection('mysql')->table('languages')->where('table_name', 'languages')->where('table_field','item_value')->where('table_key',$itemKey)->where('lang_tag',$lang)->first();

        if(empty($languagesItem)){
            $tag = self::conversionLangTag($lang);
            $langCode = $tag['lang_code'];
            $areaCode = $tag['area_code'];
            $upInfo = [
                'table_name'   => 'languages',
                'table_field'   => 'item_value',
                'table_key'   => $itemKey,
                'lang_tag'   => $lang,
                'lang_code' => $langCode,
                'area_code' => $areaCode ?? NULL,
                'lang_content' => $content,
            ];
            DB::table('languages')->where('item_key', $itemKey)->insert($upInfo);
        }else{
            $upInfo = [
                'lang_content'   => $content,
            ];
            DB::table('languages')->where('id', $languagesItem->id)->update($upInfo);
        }

        return true;
    }

    // 获取配置
    public static function configGet($itemKey,$columnName){
        $columnValue =  \Illuminate\Support\Facades\DB::table('configs')->where('item_key', $itemKey)->value($columnName);

        if(empty($columnValue)){
            return '';
        }

        return $columnValue;
    }

    // 获取配置
    public static function configLangGet($itemKey,$lang){
        $langItem =  \Illuminate\Support\Facades\DB::table('languages')->where('table_name', 'languages')->where('table_field','item_value')->where('table_key',$itemKey)->where('lang_tag',$lang)->first();

        if(empty($langItem)){
            return '';
        }

        return $langItem->lang_content;
    }


    //截取标签
    public static function conversionLangTag($langTag)
    {
        if(strstr($langTag,'zh-Hans') || strstr($langTag,'zh-Hant')){
            $tagArr = explode('-',$langTag);
            if(count($tagArr) == 3){
                $areaCode = array_pop($tagArr);
                $langCode = str_replace("-$areaCode",'',$langTag);
            } else {
                $areaCode = NULL;
                $langCode = $langTag;
            }

        } else {
            $tagArr = explode('-',$langTag);
            if(count($tagArr) == 2){
                $areaCode = array_pop($tagArr);
                $langCode = str_replace("-$areaCode",'',$langTag);
            } else {
                $areaCode = NULL;
                $langCode = $langTag;
            }
        }

        $data['area_code'] = $areaCode;
        $data['lang_code'] = $langCode;

        return $data;
    }
}
