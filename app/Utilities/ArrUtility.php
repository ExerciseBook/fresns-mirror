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
    public static function get(?array $arrays = [], string $key, string|array $values)
    {
        if (empty($arrays)) {
            return [];
        }

        $values = (array) $values;

        [$findData, $otherData] = collect($arrays)->partition(function ($item) use ($key, $values) {
            return in_array($item[$key], $values);
        });

        $data = $findData->values()->toArray();

        if (count($data) == 1) {
            return $data[0];
        }

        return $data;
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
    public static function forget(?array &$arrays = [], string $key, string|array $values)
    {
        if (empty($arrays)) {
            return false;
        }

        $values = (array) $values;

        [$findData, $otherData] = collect($arrays)->partition(function ($item) use ($key, $values) {
            return in_array($item[$key], $values);
        });

        $arrays = $otherData->values()->toArray();

        return true;

        // $key = 'code'
        // $values = ['decorate', 'verifiedIcon']
    }

    // remove key value
    public static function pull(?array &$arrays = [], string $key, string|array $values)
    {
        if (empty($arrays)) {
            return [];
        }

        $values = (array) $values;

        [$findData, $otherData] = collect($arrays)->partition(function ($item) use ($key, $values) {
            return in_array($item[$key], $values);
        });

        $arrays = $otherData->values()->toArray();

        $data = $findData->values()->toArray();

        if (count($data) == 1) {
            return $data[0];
        }

        return $data;

        // $key = 'code'
        // $values = ['decorate', 'verifiedIcon']
    }
}
