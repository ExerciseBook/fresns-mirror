<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsHashtags;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'hashtags';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'huri' => ['field' => 'slug', 'op' => '='],
        'viewCountGt' => ['field' => 'view_count', 'op' => '>='],
        'viewCountLt' => ['field' => 'view_count', 'op' => '<='],
        'likeCountGt' => ['field' => 'like_count', 'op' => '>='],
        'likeCountLt' => ['field' => 'like_count', 'op' => '<='],
        'followCountGt' => ['field' => 'follow_count', 'op' => '>='],
        'followCountLt' => ['field' => 'follow_count', 'op' => '<='],
        'shieldCountGt' => ['field' => 'shield_count', 'op' => '>='],
        'shieldCountLt' => ['field' => 'shield_count', 'op' => '<='],
        'postCountGt' => ['field' => 'post_count', 'op' => '>='],
        'postCountLt' => ['field' => 'post_count', 'op' => '<='],
        'essenceCountGt' => ['field' => 'essence_count', 'op' => '>='],
        'essenceCountLt' => ['field' => 'essence_count', 'op' => '<='],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'uuid' => 'uuid',
        'name' => 'name',
        'description' => 'description',
        'cover_file_id' => 'cover_file_id',
        'cover_file_url' => 'cover_file_url',
        'member_id' => 'member_id',
        'view_count' => 'view_count',
        'like_count' => 'like_count',
        'follow_count' => 'follow_count',
        'shield_count' => 'shield_count',
        'post_count' => 'post_count',
        'comment_count' => 'comment_count',
        'essence_count' => 'essence_count',
        'is_enable' => 'is_enable',
    ];
}
