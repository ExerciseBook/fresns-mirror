<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsMemberFollows;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'member_follows';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [

    ];

    //目标类型
    const FOLLOW_TYPE_1 = 1; //成员
    const FOLLOW_TYPE_2 = 2; //小组
    const FOLLOW_TYPE_3 = 3; //话题
    const FOLLOW_TYPE_4 = 4; //帖子
    const FOLLOW_TYPE_5 = 5; //评论

    //每次输出数量
    const INPUTTIPS_COUNT = 20;

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',

    ];
}
