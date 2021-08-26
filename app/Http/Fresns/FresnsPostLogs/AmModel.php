<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPostLogs;

use App\Base\Models\BaseAdminModel;
use Illuminate\Support\Facades\DB;

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

    public function getRawSqlQuery()
    {
        $mid = request()->header('mid');
        $query = DB::table(AmConfig::CFG_TABLE)
            ->where('deleted_at', null)
            ->where('member_id', $mid);
        $request = request();
        // 1.草稿+审核拒绝（status=1+4） / 2.审核中（status=2）
        $status = $request->input('status');
        if ($status == 1) {
            $query->where('status', 1);
            $query->orWhere('status', 4);
        } else {
            $query->where('status', 2);
        }
        $class = $request->input('class');
        // dd($class);
        if ($class == 1) {
            $query->where('post_id', null);
        } else {
            $query->where('post_id', '!=', null);
        }
        $query->orderBy('id', 'asc');

        return $query;
    }

    // 搜索排序字段
    public function initOrderByFields()
    {
        $orderByFields = [
            'id' => 'DESC',
            // 'updated_at'    => 'DESC',
        ];

        return $orderByFields;
    }
}
