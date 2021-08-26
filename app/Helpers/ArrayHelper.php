<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

class ArrayHelper
{
    /**
     * 多维数组排序
     * 第一个参数为要排序的数组，剩下是要排序的键（key）和排序方法，键的话因为要应对多维的情况，所以需要上下级连接，用`.`.
     * @example multiDimensionSort($arr,'price',SORT_DESC,'top1.field',SORT_ASC)
     * @param mixed ...$args
     * @return mixed
     */
    public static function multiDimensionSort(...$args)
    {
        $arr = array_shift($args); // 取到要排序的数组，剩下的为要排序的键和排序类型
        $sort_arg = [];
        foreach ($args as $arg) {
            // 这里主要是为了得到排序的key对应的值
            $sort = $arr;
            if (is_string($arg)) {
                $arg = explode('.', $arg); // 我设定参数里面多维数组下的键，用‘.’连接下级的键，这里得到键，然后下面循环取得数组$arr里面该键对应的值
                foreach ($arg as $key) {
                    $sort = array_column($sort, $key); // 每次循环$sort的维度就会减一
                }
                $sort_arg[] = $sort;
            } else {
                $sort_arg[] = $arg; // 排序方法SORT_ASC、SORT_DESC等
            }
        }
        $sort_arg[] = &$arr; // 这个数组大致结构为：[$sort, SORT_ASC, $sort2, SORT_DESC,$arr]

        call_user_func_array('array_multisort', $sort_arg);

        return $arr;
    }

    /**
     * 二维数组根据某个字段排序.
     * @param array $array 要排序的数组
     * @param string $keys   要排序的键字段
     * @param string $sort  排序类型  SORT_ASC     SORT_DESC
     * @return array 排序后的数组
     */
    public static function arraySort(&$array, $keys, $sortDirection)
    {
        $keysValue = [];
        foreach ($array as $k => $v) {
            $keysValue[$k] = intval($v[$keys]);
        }
        array_multisort($keysValue, $sortDirection, $array);

        return $array;
    }

    // object to array
    public static function objectToArray($obj)
    {
        $a = json_encode($obj);
        $b = json_decode($a, true);

        return $b;
    }

    // 获取描述
    public static function keyDescInArray($key, $arr, $matchKey = 'key', $descKey = 'text')
    {
        foreach ($arr as $item) {
            if (! is_array($item)) {
                $item = self::objectToArray($item);
            }
            if (isset($item[$matchKey]) && $item[$matchKey] == $key) {
                return  $item[$descKey] ?? '未知';
            }
        }

        return '未知';
    }
}
