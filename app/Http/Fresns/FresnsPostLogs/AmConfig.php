<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsPostLogs;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'post_logs';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'inStatus' => ['field' => 'status', 'op' => 'IN'],
        'logId' => ['field' => 'id', 'op' => '='],
        'ids' => ['field' => 'id', 'op' => 'IN'],
        'post_id' => ['field' => 'post_id', 'op' => '='],
        'member_id' => ['field' => 'member_id', 'op' => '='],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'member_id' => 'member_id',
        'post_id' => 'post_id',
        'platform_id' => 'platform_id',
        'group_id' => 'group_id',
        'type' => 'type',
        'title' => 'title',
        'content' => 'content',
        'is_markdown' => 'is_markdown',
        'is_anonymous' => 'is_anonymous',
        'editor_json' => 'editor_json',
        'comment_set_json' => 'comment_set_json',
        'allow_json' => 'allow_json',
        'location_json' => 'location_json',
        'files_json' => 'files_json',
        'extends_json' => 'extends_json',
        'status' => 'status',
        'reason' => 'reason',
        'submit_at' => 'submit_at',
    ];
}
