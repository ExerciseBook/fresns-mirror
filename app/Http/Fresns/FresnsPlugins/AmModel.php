<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPlugins;

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

    //根据昵称获取插件
    public static function buildSelectOptionsByUnikey($scene): array
    {
        if (empty($scene)) {
            return [];
        }

        $opts = DB::table(AmConfig::CFG_TABLE)
            ->select('unikey AS key', 'name AS text')
            ->where('scene', 'LIKE', "%$scene%")
            ->where('deleted_at', null)
            ->get()->toArray();

        return $opts;
    }


    public function initOrderByFields()
    {
        $orderByFields = [
            // 'rank_num' => 'ASC',
            'id' => 'ASC',
            // 'updated_at'    => 'DESC',
        ];
        return $orderByFields;
    }
}

