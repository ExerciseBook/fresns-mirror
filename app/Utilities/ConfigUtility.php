<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Models\CodeMessage;
use App\Models\Config;

class ConfigUtility
{
    // add config items
    public static function addFresnsConfigItems($fresnsConfigItems)
    {
        foreach ($fresnsConfigItems as $item) {
            $config = Config::where('item_key', '=', $item['item_key'])->first();
            if (empty($config)) {
                Config::insert($item);
            }
        }
    }

    // remove config items
    public static function removeFresnsConfigItems($fresnsConfigKeys)
    {
        foreach ($fresnsConfigKeys as $item) {
            Config::where('item_key', '=', $item)
                ->where('is_custom', 1)
                ->forceDelete();
        }
    }

    // change config items
    public static function changeFresnsConfigItems(array $fresnsConfigKeys)
    {
        // 批量修改常规的配置（存在则修改，不存在则新建）
        foreach($fresnsConfigKeys as $item) {
            $config = Config::updateOrCreate(['item_key' => $item['item_key']], $item);
        }
    }

    // change config multilingual items
    public static function changeFresnsConfigMultilingualItem($fresnsConfigKey)
    {
        // 修改多语言的配置（存在则修改，不存在则新建）
    }

    // add config item array items
    public static function addFresnsConfigItemArrayItems($itemKey, $unique, $items)
    {
        // 在配置项的 Json 数组中新增多条新项

        // $itemKey = language_pack
        // $unique = name
        // $items = [{"name":"newName","canDelete":false},{"name":"newName2","canDelete":false}]

        // 将 items 数组中多条新增到 language_pack 数组中。以 unique 定义的键名为唯一值，存在则跳过，不存在则新增。
    }

    // change config item array key
    public static function changeFresnsConfigItemArrayKey($itemKey, $keyName, $newKeyName)
    {
        // 修改数组配置项的键名（数组中有多个键名批量修改）
        // [{"name":"language","canDelete":false},{"name":"errorUnknown","canDelete":false}]

        // $itemKey = language_pack
        // $keyName = name
        // $newKeyName = newName

        // [{"newName":"language","canDelete":false},{"newName":"errorUnknown","canDelete":false}]
    }

    // change config item array value
    public static function changeFresnsConfigItemArrayValue($itemKey, $keyName, $keyValue, $newKeyValue)
    {
        // 修改数组配置项的键值
        // [{"name":"language","canDelete":false},{"name":"errorUnknown","canDelete":false}]

        // $itemKey = language_pack
        // $keyName = name
        // $keyValue = language
        // $newKeyValue = language123

        // [{"name":"language123","canDelete":false},{"name":"errorUnknown","canDelete":false}]
    }

    // add config item object items
    public static function addFresnsConfigItemObjectItems($itemKey, $items)
    {
        // 在配置项的 Json 对象中新增多条新项

        // $itemKey = zh-Hans
        // $items = {"language":"语言","errorUnknown":"未知错误"}

        // 将 items 对象中多条新增到 zh-Hans 对象中。存在则跳过，不存在则新增。
    }

    // change config item object key
    public static function changeFresnsConfigItemObjectKey($itemKey, $keyName, $newKeyName)
    {
        // 修改对象配置项的键名
        // {"language":"语言","errorUnknown":"未知错误"}

        // $itemKey = zh-Hans
        // $keyName = language
        // $newKeyName = language123

        // {"language123":"语言","errorUnknown":"未知错误"}
    }

    // change config item object value
    public static function changeFresnsConfigItemObjectValue($itemKey, $keyName, $newKeyValue)
    {
        // 修改对象配置项的键值
        // {"language":"语言","errorUnknown":"未知错误"}

        // $itemKey = zh-Hans
        // $keyName = language
        // $newKeyValue = 语言123

        // {"language":"语言123","errorUnknown":"未知错误"}
    }

    // get code message
    public static function getCodeMessage(int $code, string $unikey = '', string $langTag = '')
    {
        $unikey = $unikey ?: 'Fresns';

        if (empty($langTag)) {
            $langTag = Config::where('item_key', 'default_language')->value('item_value');
        }

        $message = CodeMessage::where('plugin_unikey', $unikey)->where('code', $code)->where('lang_tag', $langTag)->value('message');

        return $message ?? 'Unknown Error';
    }
}
