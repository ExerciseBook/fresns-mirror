<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsMemberLikes;

// 配置
use App\Base\Config\BaseConfig;
 

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'member_likes';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'member_id' => ['field' => 'member_id', 'op' => '='],
        'type' => ['field' => 'like_type', 'op' => '='],
        'like_id' => ['field' => 'like_id', 'op' => '='],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'member_id' => 'member_id',
        'like_type' => 'like_type',
        'like_id' => 'like_id',
    ];

     

}