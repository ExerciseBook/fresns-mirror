<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Base\Models\BaseModel;
use App\Http\Share\Common\ValidateService;
use Illuminate\Support\Facades\DB;

/**
 * Class SearchHelper.
 *
 * search helper 搜索条件形式统一为 $condArr
 *
 * [
 *  ['field' => 'id', 'op' => '=',  'value' => '3'],
 *  ['field' => 'id', 'op' => 'IN', 'value' => [5, 8]],
 *  ['field' => 'name', 'op' => 'LIKE', 'value' => '%str%'],
 * ]
 */
class SearchHelper
{
    // 为附表搜索 初始化搜索条件
    public static function buildCondForAppendSearch($searchableFields)
    {
        $whereCond = [];
        $req = request();
        foreach ($searchableFields as $searchField => $arr) {
            // 无key跳过
            if (! $req->has($searchField)) {
                continue;
            }

            $searchValue = $req->input($searchField);

            // 无值跳过
            if ($searchValue === null) {
                continue;
            }

            // 验证数组字段
            ValidateService::validParamExist($arr, ['field', 'op']);

            $op = $arr['op'];

            $upperOp = strtoupper($op);

            switch ($upperOp) {
                case 'IN':
                    $inArr = explode(',', $searchValue);
                    $searchValue = $inArr;
                    break;
                case 'LIKE':
                    $searchValue = '%'.$searchValue.'%';
                    break;
                default:
                    $searchValue = $searchValue;
                    break;
            }

            $arr['value'] = $searchValue;
            $whereCond[$searchField] = $arr;
        }

        return $whereCond;
    }

    // 根据条件查询获取某列
    public static function doSimpleQueryPluckField($table, $condArr, $field = 'id'): array
    {
        $query = self::getQuery($table, $condArr);

        return $query->pluck($field)->toArray();
    }

    // 根据条件查询
    public static function doSimpleQuery($table, $condArr, $limit = 30000): array
    {
        // 验证数组字段
        foreach ($condArr as $cond) {
            ValidateService::validParamExist($cond, ['field', 'op', 'value']);
        }

        $query = self::getQuery($table, $condArr);

        $dataObjArr = $query->limit($limit)->get();
        if (count($dataObjArr) > 0) {
            return $dataObjArr->toArray();
        }

        return [];
    }

    // 获取执行查询的对象
    private static function getQuery($table, $condArr)
    {
        $query = DB::table($table)->where('deleted_at', null);
        // 执行操作
        foreach ($condArr as $cond) {
            $field = $cond['field'];
            $op = $cond['op'];
            $searchValue = $cond['value'];

            $upperOp = strtoupper($op);

            switch ($upperOp) {
                case '=':
                    $query = $query->where($field, $searchValue);
                    break;
                case 'IN':
                    $inArr = $searchValue;
                    $query = $query->whereIn($field, $inArr);
                    break;
                case '>=':
                    $query = $query->where($field, '>=', $searchValue);
                    break;
                case '<=':
                    $query = $query->where($field, '<=', $searchValue);
                    break;
                case 'LIKE':
                    $query = $query->whereRaw($field.' LIKE ?', ['%'.$searchValue.'%']);
                    break;
            }
        }

        return $query;
    }
}
