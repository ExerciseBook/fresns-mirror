<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Models\CodeMessage;
use App\Models\Config;
use App\Models\Language;

class ConfigUtility
{
    // add config items
    public static function addFresnsConfigItems(array $fresnsConfigItems)
    {
        foreach ($fresnsConfigItems as $item) {
            $config = Config::where('item_key', '=', $item['item_key'])->first();
            if (empty($config)) {
                Config::insert($item);
            }
        }
    }

    // remove config items
    public static function removeFresnsConfigItems(array $fresnsConfigKeys)
    {
        foreach ($fresnsConfigKeys as $item) {
            Config::where('item_key', '=', $item)->where('is_custom', 1)->forceDelete();
        }
    }

    // change config items
    public static function changeFresnsConfigItems(array $fresnsConfigKeys)
    {
        foreach($fresnsConfigKeys as $item) {
            Config::updateOrCreate(
                ['item_key' => $item['item_key']],
                collect($item)->only('item_key', 'item_value', 'item_type', 'item_tag', 'is_multilingual')->toArray()
            );

            if ($item['is_multilingual']) {
                foreach($item['language_values'] as $language) {
                    $languageItem = [
                        'table_key' => $item['item_key'],
                        'lang_tag' => $language['lang_tag'],
                        'table_name' => 'configs',
                        'table_column' => 'item_value',
                        'lang_content' => $language['lang_content'],
                    ];

                    self::changeFresnsConfigMultilingualItem($languageItem);
                }
            }
        }
    }

    // change config multilingual items
    public static function changeFresnsConfigMultilingualItem($fresnsConfigKey)
    {
        // 修改多语言的配置（存在则修改，不存在则新建）
        $language = Language::ofConfig()
            ->where('table_key', $fresnsConfigKey['table_key'])
            ->where('lang_tag', $fresnsConfigKey['lang_tag'])
            ->first();

        if (! $language) {
            $language = new Language();
            $language->fill($fresnsConfigKey);
        }

        $language->lang_content = $fresnsConfigKey['lang_content'];
        $language->save();

        return $language;
    }

    // get code message
    public static function getCodeMessage(int $code, ?string $unikey = null, ?string $langTag = null)
    {
        $unikey = $unikey ?: 'Fresns';

        if (empty($langTag)) {
            $langTag = Config::where('item_key', 'default_language')->value('item_value');
        }

        $message = CodeMessage::where('plugin_unikey', $unikey)->where('code', $code)->where('lang_tag', $langTag)->value('message');

        return $message ?? 'Unknown Error';
    }
}
