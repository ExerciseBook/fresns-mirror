<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsHashtags;

use App\Base\Models\BaseAdminModel;


class AmModel extends BaseAdminModel
{
    protected $table = AmConfig::CFG_TABLE;

    // 前台表单字段映射
    public function formFieldsMap()
    {
        return AmConfig::FORM_FIELDS_MAP;
    }

    // 新增搜索条件
    public function getAddedSearchableFields()
    {
        return AmConfig::ADDED_SEARCHABLE_FIELDS;
    }

    // hook-添加之后
    public function hookStoreAfter($id)
    {

    }

    // 搜索排序字段
    public function initOrderByFields()
    {
        $sortType = request()->input('sortType', "");
        $sortWay = request()->input('sortDirection', 2);
        $sortWayType = $sortWay == 2 ? "DESC" : "ASC";
        switch ($sortType) {
            case 'view':
                $orderByFields = [
                    'view_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];
                return $orderByFields;
                break;
            case 'like':
                $orderByFields = [
                    'like_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];
                return $orderByFields;
                break;
            case 'follow':
                $orderByFields = [
                    'follow_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];
                return $orderByFields;
                break;
            case 'shield':
                $orderByFields = [
                    'shield_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];
                return $orderByFields;
                break;
            case 'post':
                $orderByFields = [
                    'post_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];
                return $orderByFields;
                break;
            case 'essence':
                $orderByFields = [
                    'essence_count' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];
                return $orderByFields;
                break;
            case 'time':
                $orderByFields = [
                    'created_at' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];
                return $orderByFields;
                break;

            default:
                $orderByFields = [
                    'created_at' => $sortWayType,
                    // 'updated_at'    => 'DESC',
                ];
                return $orderByFields;
                break;
        }
    }
}

