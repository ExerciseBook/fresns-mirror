<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Content;

class AmConfig
{
    const GROUP_FOLLOW_NAME = 'follow_group_name';
    const GROUP_LIKE_NAME = 'like_group_name';
    const GROUP_SHIELD_NAME = 'shield_group_name';
    const POST_LIKE_NAME = 'like_post_name';
    const POST_FOLLOW_NAME = 'follow_post_name';
    const POST_SHIELD_NAME = 'shield_post_name';
    const HASHTAG_FOLLOW_NAME = 'hashtag_follow_name';
    const HASHTAG_LIKE_NAME = 'hashtag_like_name';
    const HASHTAG_SHIELD_NAME = 'hashtag_shield_name';

    const COMMENT_NAME = 'comment_name';
    const COMMENT_FOLLOW_NAME = 'follow_comment_name';
    const COMMENT_LIKE_NAME = 'like_comment_name';
    const COMMENT_SHIELD_NAME = 'shield_comment_name';
    const POST_HOT = 'post_hot';
    const GROUP_FOLLOW = 'group_follow';
    const FOLLOW_GROUP_SETTING = 'follow_group_setting';
    const FOLLOW_POST_SETTING = 'follow_post_setting';
    const LIKE_GROUP_SETTING = 'like_group_setting';
    const SHIELD_SETTING = 'shield_group_setting';
    const FOLLOW_COMMENT_SETTING = 'follow_comment_setting';
    const LIKE_COMMENT_SETTING = 'like_comment_setting';
    const SHIELD_COMMENT_SETTING = 'shield_comment_setting';
    const GROUP_NAME = 'group_name';
    const POST_NAME = 'post_name';
    const POST_EDIT = 'post_edit';
    const POST_EDIT_TIMELIMIT = 'post_edit_timelimit';
    const POST_EDIT_STICKY = 'post_edit_sticky';
    const POST_EDIT_ESSENCE = 'post_edit_essence';
    const COMMENT_EDIT_TIMELIMIT = 'comment_edit_timelimit';
    const COMMENT_EDIT_STICKY = 'comment_edit_sticky';
    const COMMENT_EDIT = 'comment_edit';
    const SNS_PROPORTION = 'sns_proportion';
    const COMMENT_PREVIEW = 'comment_preview';
    const SHIELD_HASHTAG_NAME = 'shield_hashtag_name';
    const HASHTAG_NAME = 'hashtag_name';
    const FOLLOW_HASHTAG_NAME = 'follow_hashtag_name';
    const LIKE_HASHTAG_SETTING = 'like_hashtag_setting';
    const LIKE_HASHTAG_NAME = 'like_hashtag_name';
    const FOLLOW_HASHTAG_SETTING = 'follow_hashtag_setting';
    const SHIELD_HASHTAG_SETTING = 'shield_hashtag_setting';
    const DEFAULT_AVATAR = 'default_avatar';
    const ANONYMOUS_AVATAR = 'anonymous_avatar';
    const DEACTIVATE_AVATAR = 'deactivate_avatar';
    const TEMPLATEID = '1234512121';
    const SITE_MODEL = 'site_mode';
    const SITE_DOMAIN = 'site_domain';
    const PRIVATE = 'private';
    const POST_DETAIL_SERVICE = 'post_detail_service';
    // 查询模式
    const QUERY_TYPE_DB_QUERY = 'db_query';  // 支持join配置的查询
    const QUERY_TYPE_SQL_QUERY = 'sql_query'; // 原生SQL查询
}
