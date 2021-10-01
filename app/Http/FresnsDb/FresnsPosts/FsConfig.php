<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsDb\FresnsPosts;

use App\Base\Config\BaseConfig;

class FsConfig extends BaseConfig
{
    // Main Table
    const CFG_TABLE = 'posts';

    // Configs item_key
    const IT_PUBLISH_POSTS = 'it_publish_posts';
    const CHECK_CONTENT = 'check_content';
    const POST_EDITOR_WORD_COUNT = 'post_editor_word_count';
    const POST_EDITOR_BRIEF_COUNT = 'post_editor_brief_count';
    const HASHTAG_SHOW = 'hashtag_show';
    const POST_COUNTS = 'post_counts';
    const HASHTAG_COUNTS = 'hashtag_counts';

    // Whether you have permission to read
    const IS_ALLOW_1 = 1;

    // Additional search columns in the main table
    const ADDED_SEARCHABLE_FIELDS = [
        'ids' => ['field' => 'id', 'op' => 'IN'],
        'pid' => ['field' => 'uuid', 'op' => '='],
        'searchKey' => ['field' => 'title', 'op' => 'LIKE'],
        'searchKey' => ['field' => 'content', 'op' => 'LIKE'],
        'searchType' => ['field' => 'types', 'op' => 'LIKE'],
        'searchEssenceType' => ['field' => 'essence_status', 'op' => 'in'],
        'searchStickyType' => ['field' => 'sticky_status', 'op' => 'in'],
        'searchMid' => ['field' => 'member_id', 'op' => '='],
        'searchMid' => ['field' => 'member_id', 'op' => '='],
        'searchGid' => ['field' => 'group_id', 'op' => '='],
        'mapId' => ['field' => 'map_id', 'op' => '='],
        'viewCountGt' => ['field' => 'view_count', 'op' => '>='],
        'viewCountLt' => ['field' => 'view_count', 'op' => '<='],
        'likeCountGt' => ['field' => 'like_count', 'op' => '>='],
        'likeCountLt' => ['field' => 'like_count', 'op' => '<='],
        'followCountGt' => ['field' => 'follow_count', 'op' => '>='],
        'followCountLt' => ['field' => 'follow_count', 'op' => '<='],
        'shieldCountGt' => ['field' => 'shield_count', 'op' => '>='],
        'shieldCountLt' => ['field' => 'shield_count', 'op' => '<='],
        'commentCountGt' => ['field' => 'comment_count ', 'op' => '>='],
        'commentCountLt' => ['field' => 'comment_count ', 'op' => '<='],
        'publishTimeGt' => ['field' => 'created_at', 'op' => '>='],
        'publishTimeLt' => ['field' => 'created_at', 'op' => '<='],
        'expired_at' => ['field' => 'created_at', 'op' => '<='],
    ];

    const APPEND_SEARCHABLE_FIELDS = [
        'searchKey' => ['field' => 'content', 'op' => 'LIKE'],
    ];

    // Model Usage - Form Mapping
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'uuid' => 'uuid',
        'member_id' => 'member_id',
        'group_id' => 'group_id',
        'types' => 'types',
        'title' => 'title',
        'content' => 'content',
        'is_brief' => 'is_brief',
        'sticky_status' => 'sticky_status',
        'essence_status' => 'essence_status',
        'is_anonymous' => 'is_anonymous',
        'is_allow' => 'is_allow',
        'more_json' => 'more_json',
        'map_service' => 'map_service',
        'map_latitude' => 'map_latitude',
        'map_longitude' => 'map_longitude',
        'map_scale' => 'map_scale',
        'map_poi' => 'map_poi',
        'view_count' => 'view_count',
        'like_count' => 'like_count',
        'follow_count' => 'follow_count',
        'shield_count' => 'shield_count',
        'comment_count' => 'comment_count',
        'comment_like_count' => 'comment_like_count',
        'latest_comment_at' => 'latest_comment_at',
        'is_enable' => 'is_enable',
    ];
}
