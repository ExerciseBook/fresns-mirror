<?php

namespace App\Http\FresnsDb\FresnsMembers;

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
