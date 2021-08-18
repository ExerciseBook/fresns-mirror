<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsComments;

// 配置
use App\Base\Config\BaseConfig;
 

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'comments';
    const IT_PUBLISH_COMMENTS = 'it_publish_comments';
    const COMMENT_EDITOR_BRIEF_COUNT = 'comment_editor_brief_count';
    const COMMENT_EDITOR_WORD_COUNT = 'comment_editor_word_count';
    const HASHTAG_SHOW = 'hashtag_show';
    const COMMENT_COUNTS = 'comment_counts';
    const HASHTAG_COUNTS = 'hashtag_counts';
    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        // 'status' => ['field' => 'status','op'=>'=' ],
        'cid' => ['field' => 'uuid', 'op' => '='],
        'ids' => ['field' => 'id', 'op' => 'IN'],
        // 'inStatus' => ['field' => 'status', 'op' => 'IN'],
    ];
    // tree 搜索条件
    protected $treeSearchRule = [
        'id' => ['field' => 'id', 'op' => '='],
    ];
    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'uuid' => 'uuid',
        'post_id' => 'post_id',
        'parent_id' => 'parent_id',
        'member_id' => 'member_id',
        'type' => 'type',
        'content' => 'content',
        'is_brief' => 'is_brief',
        // 'status'  => 'status',
        'is_anonymous' => 'is_anonymous',
        'is_lbs' => 'is_lbs',
        'is_sticky' => 'is_sticky',
        // 'label_file_id'  => 'label_file_id',
        // 'label_file_url'  => 'label_file_url',
        'more_json' => 'more_json',
        'like_count' => 'like_count',
        'follow_count' => 'follow_count',
        'shield_count' => 'shield_count',
        'comment_count' => 'comment_count',
        'comment_like_count' => 'comment_like_count',
        // 'release_at'  => 'release_at',
        'latest_edit_at' => 'latest_edit_at',
        'latest_comment_at' => 'latest_comment_at',
        'is_enable' => 'is_enable',
    ];

     
}