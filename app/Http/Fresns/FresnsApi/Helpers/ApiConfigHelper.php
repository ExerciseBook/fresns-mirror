<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Helpers;

use App\Helpers\StrHelper;
use App\Http\Fresns\FresnsApi\Base\FresnsBaseConfig;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsLanguages\FresnsLanguagesService;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;

class ApiConfigHelper
{
    //获取系统配置信息
    public static function getConfigsList()
    {
        $map = config(FresnsBaseConfig::CONFIGS_LIST);

        return $map;
    }

    //获取系统配置信息api
    public static function getConfigsListApi()
    {
        $map = config(FresnsBaseConfig::CONFIGS_LIST_API);

        $itemArr = [];
        if (!empty($map)) {
            foreach ($map as $v) {
                if ($v['is_restful'] == 0) {
                    continue;
                }
                $item = [];
                $item['itemKey'] = $v['item_key'];
                $itemValue = $v['item_value'];
                $isJson = StrHelper::isJson($itemValue);
                if ($isJson == true) {
                    $itemValue = json_decode($itemValue, true);
                }
                $item['itemValue'] = $itemValue;
                $item['itemTag'] = $v['item_tag'];
                $item['itemType'] = $v['item_type'];
                $item['itemStatus'] = boolval($v['is_enable']);
                $item['isMultilingual'] = $v['is_multilingual'];
                $itemArr[] = $item;
            }
        }

        return $itemArr;
    }

    //列表数据
    public static function getConfigsListsApi()
    {
        $map = self::getConfigsListApi();
        $itemArr = [];
        if ($map) {
            foreach ($map as $k => $v) {
                $itemArr[] = self::joinData($v);
            }
        }

        return $itemArr;
    }

    //通过配置组键名返回
    public static function getConfigByKey($key)
    {
        $map = config(FresnsBaseConfig::CONFIGS_LIST);
        $itemArr = [];
        if ($map) {
            foreach ($map as $k => $v) {
                if ($k == $key) {
                    $itemArr = $v;
                }
            }
        }

        return $itemArr;
    }

    public static function getConfigByKeyApi($key)
    {
        $map = self::getConfigsListApi();
        $itemArr = [];
        if ($map) {
            foreach ($map as $k => $v) {
                if ($v['itemTag'] == $key) {
                    $itemArr[] = self::joinData($v);
                }
            }
        }

        return $itemArr;
    }

    //通过键名返回对应的键名键值
    public static function getConfigByItemKey($itemKey)
    {
        $map = config(FresnsBaseConfig::CONFIGS_LIST);
        $data = null;
        if ($map) {
            foreach ($map as $k => $v) {
                if (isset($v[$itemKey])) {
                    $data = $v[$itemKey];
                }
            }
        }
        return $data;
    }

    public static function getConfigByItemKeyApi($itemKey)
    {
        $map = self::getConfigsListApi();
        $data = [];
        if ($map) {
            foreach ($map as $k => $v) {
                if ($v['itemKey'] == $itemKey) {
                    $data[] = self::joinData($v);
                }
            }
        }
        return $data;
    }

    //组装api返回数据
    public static function joinData($data)
    {
        $langTag = ApiLanguageHelper::getLangTagByHeader();

        $item['itemKey'] = $data['itemKey'];
        $item['itemValue'] = $data['itemValue'];
        //当 is_multilingual 字段为 1 时，代表该键值为多语言
        if ($data['isMultilingual'] == 1) {
            $item['itemValue'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', $item['itemKey'], $langTag);
        }
        if ($data['itemType'] == 'number') {
            if (is_numeric($item['itemValue'])) {
                $item['itemValue'] = intval($item['itemValue']);
            }
        }
        //当 item_type 字段为 file 时，如果键值是以 http:// 或 https:// 开头，则不用特殊处理，原样输出。如果是数字，则代表是文件 ID，凭 ID 输出文件 URL，如果该文件类型开启了防盗链功能，则向插件索要 URL 输出。
        if ($data['itemType'] == 'file') {
            if (is_numeric($item['itemValue'])) {
                $item['itemValue'] = ApiFileHelper::getImageSignUrlByFileId($item['itemValue']);
            }
        }
        //plugin 类型代表是插件 unikey 值，凭 unikey 输出插件 URL。plugins 类型代表是多选插件，以英文逗号隔开。
        //判断 plugins > plugin_domain 是否有值，
        //有值则是 plugin_domain + access_path 字段内容拼接成完整 URL；
        //无值则是拿配置表 backend_domain 键值 + 插件表 access_path 字段拼接成完整 URL
        if ($data['itemType'] == 'plugin') {
            $plugin = FresnsPlugin::where('unikey', $item['itemValue'])->first();
            if ($plugin) {
                if (!empty($plugin['plugin_domain'])) {
                    $domain = $plugin['plugin_domain'];
                } else {
                    $domain = ApiConfigHelper::getConfigByItemKey('backend_domain');
                }
                $value['unikey'] = $plugin['unikey'];
                $value['url'] = $domain.$plugin['access_path'];
                $item['itemValue'] = $value;
            }
        }

        if ($data['itemType'] == 'plugins') {
            $unikeyArr = explode(',', $item['itemValue']);
            $pluginArr = FresnsPlugin::whereIn('unikey', $unikeyArr)->get([
                'unikey',
                'plugin_domain',
                'access_path'
            ])->toArray();
            if ($pluginArr) {
                $domain = ApiConfigHelper::getConfigByItemKey('backend_domain');
                $itArr = [];
                foreach ($pluginArr as $v) {
                    $it = [];
                    $it['unikey'] = $v['unikey'];
                    if (!empty($v['plugin_domain'])) {
                        $domain = $v['plugin_domain'];
                    }
                    $it['url'] = $domain.$v['access_path'] ?? '';
                    $itArr[] = $it;
                }
                $item['itemValue'] = $itArr;
            }
        }
        $item['itemTag'] = $data['itemTag'];
        $item['itemType'] = $data['itemType'];
        $item['itemStatus'] = $data['itemStatus'];


        return $item;
    }


    //获取所有的语言参数
    public static function getConfigsLanguageList()
    {
        $map = config(FresnsBaseConfig::CONFIGS_LIST);
        $data = [];
        foreach ($map as $k => $v) {
            if ($k == FresnsConfigsConfig::LANGUAGE) {
                $data = $v;
            }
        }

        return $data;
    }

    // 获取距离单位
    public static function distanceUnits($langTag)
    {
        $language = self::getConfigsLanguageList();
        $distanceUnits = '';
        // 获取默认语言的距离单位
        foreach ($language['language_menus'] as $f) {
            if ($f['langTag'] == $language['default_language']) {
                $distanceUnits = $f['lengthUnits'];
            }
        }
        foreach ($language['language_menus'] as $v) {
            if ($v['langTag'] = $langTag) {
                if (!empty($v['lengthUnits'])) {
                    $distanceUnits = $v['lengthUnits'];
                }
            }
        }
        return $distanceUnits;
    }
}
