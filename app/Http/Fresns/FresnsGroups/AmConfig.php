<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsGroups;

// 配置
use App\Base\Config\BaseConfig;

class AmConfig extends BaseConfig
{
    // 主表
    const CFG_TABLE = 'groups';

    // 主表额外搜索字段
    const ADDED_SEARCHABLE_FIELDS = [
        'ids' => ['field' => 'id', 'op' => 'in'],
        'gid' => ['field' => 'uuid', 'op' => '='],
        'parentId' => ['field' => 'parent_id', 'op' => '='],
        // 'recommend' => ['field' => 'is_recommend','op' => '='],
        'pid' => ['field' => 'parent_id', 'op' => '='],
        'recommend' => ['field' => 'is_recommend', 'op' => '='],
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
        'createdTimeGt' => ['field' => 'created_at', 'op' => '>='],
        'createdTimeLt' => ['field' => 'created_at', 'op' => '<='],
        // 'is_recommend' => ['field' => 'is_recommend', 'op' => '<='],
    ];

    // model 使用 - 表单映射
    const FORM_FIELDS_MAP = [
        'id' => 'id',
        'uuid' => 'uuid',
        'parent_id' => 'parent_id',
        'name' => 'name',
        'description' => 'description',
        'type' => 'type',
        'type_mode' => 'type_mode',
        'type_find' => 'type_find',
        'type_follow' => 'type_follow',
        'plugin_unikey' => 'plugin_unikey',
        'cover_file_id' => 'cover_file_id',
        'cover_file_url' => 'cover_file_url',
        'banner_file_id' => 'banner_file_id',
        'banner_file_url' => 'banner_file_url',
        'rank_num' => 'rank_num',
        'is_recommend' => 'is_recommend',
        'recom_rank_num' => 'recom_rank_num',
        'allow_view' => 'allow_view',
        'allow_post' => 'allow_post',
        'allow_comment' => 'allow_comment',
        'admin_members' => 'admin_members',
        'permission' => 'permission',
        'view_count' => 'view_count',
        'like_count' => 'like_count',
        'follow_count' => 'follow_count',
        'shield_count' => 'shield_count',
        'post_count' => 'post_count',
        'essence_count' => 'essence_count',
        'is_enable' => 'is_enable',
    ];
    const RECOMMEND_OPTION = [
        ['key' => 1, 'text' => '不推荐'],
        ['key' => 2, 'text' => '推荐'],
    ];
    const TYPE_MODE = [
        ['key' => 1, 'text' => '公开（任何人都能查看小组内帖子）'],
        ['key' => 2, 'text' => '非公开（只有成员才能查看小组内帖子）'],
    ];
    const TYPE_FOLLOW = [
        ['key' => 1, 'text' => '原生方式'],
        ['key' => 2, 'text' => '插件方式'],
    ];
    const TYPE_FIND = [
        ['key' => 1, 'text' => '可发现（任何人都能找到这个小组）'],
        ['key' => 2, 'text' => '不可发现（只有成员能找到这个小组）'],
    ];
    const PUBLISH_POST = [
        ['key' => 1, 'text' => '所有人'],
        ['key' => 2, 'text' => '仅关注了小组的成员'],
        ['key' => 3, 'text' => '仅指定的角色成员'],
    ];
}
