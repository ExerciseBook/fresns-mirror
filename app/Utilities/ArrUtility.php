<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

class ArrUtility
{
    // get key value
    public static function getKeyValue(?array &$arrays = [], string $key, array $values)
    {
        if (empty($arrays)) {
            return [];
        }

        [$findData, $otherData] = collect($arrays)->partition(function ($item) use ($key, $values) {
            return in_array($item[$key], $values);
        });

        $arrays = $otherData->toArray();

        return $findData->toArray();

        // $arrays
        // [
        //     {
        //         "code": "decorate",
        //         "style": "operations->style",
        //         "name": "operations->name",
        //         "description": "operations->description",
        //         "imageUrl": "operations->image_file_id or image_file_url",
        //         "imageActiveUrl": "operations->image_active_file_id or image_active_file_url",
        //         "displayType": "operations->display_type",
        //         "pluginUrl": "operations->plugin_unikey",
        //     }
        // ]

        // $key = 'code'
        // $values = ['decorate', 'verifiedIcon']
    }

    // remove key value
    public static function removeKeyValue(?array $arrays = [], string $key, array $values)
    {
        if (empty($arrays)) {
            return [];
        }

        [$findData, $otherData] = collect($arrays)->partition(function ($item) use ($key, $values) {
            return in_array($item[$key], $values);
        });

        $arrays = $otherData->toArray();

        return $findData->toArray();

        // $key = 'code'
        // $values = ['decorate', 'verifiedIcon']
    }
}
