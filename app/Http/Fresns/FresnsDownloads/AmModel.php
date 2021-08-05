<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsDownloads;

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
        $sortWay = request()->input('sortWay', 2);
        $sortWayType = $sortWay == 2 ? "DESC" : "ASC";
        $orderByFields = [
            'created_at' => $sortWayType,
            // 'updated_at'    => 'DESC',
        ];
        return $orderByFields;
    }
}

