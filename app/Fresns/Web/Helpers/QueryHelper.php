<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Helpers;

use App\Helpers\ConfigHelper;
use Illuminate\Pagination\LengthAwarePaginator;

class QueryHelper
{
    public static function convertOptionToRequestParam(string $type, array $requestQuery)
    {
        $queryState = ConfigHelper::fresnsConfigByItemKey("menu_{$type}_query_state");
        $queryConfig = ConfigHelper::fresnsConfigByItemKey("menu_{$type}_query_config");

        // 转换为数组参数，封装
        $params = [];
        if (! empty($queryConfig)) {
            $urlInfo = parse_url($queryConfig);

            if (! empty($urlInfo['query'])) {
                parse_str($urlInfo['query'], $query);
            }
        }

        // 默认不允许用户传递参数
        $clientQuery = [];

        // 禁止客户端传递参数改变数据进行请求
        if ($queryState == 1) {
            $clientQuery = [];
        }

        // 只允许用户传递翻页参数
        if ($queryState == 2) {
            $clientQuery = [
                'pageSize' => $requestQuery['pageSize'] ?? 15,
                'page' => $requestQuery['page'] ?? 1,
            ];
        }

        // 允许用户传递所有参数
        if ($queryState == 3) {
            $clientQuery = $requestQuery;
        }

        // 参数覆盖
        return array_merge($params, $clientQuery);
    }

    public static function convertApiDataToPaginate($items, $paginate)
    {
        if (method_exists($items, 'toArray')) {
            $items = $items->toArray();
        }

        $items = (array) $items;
        $total = $paginate['total'] ?? 0;
        $pageSize = $paginate['pageSize'] ?? 15;

        $paginate = new LengthAwarePaginator(
            items: $items,
            total: $total,
            perPage: $pageSize,
            currentPage: \request('page', 1),
        );

        $paginate->withPath('/'.\request()->path())->withQueryString();

        return $paginate;
    }
}
