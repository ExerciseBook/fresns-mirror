<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Editor;

class AmConfig
{
    const SITE_MODEL = 'site_mode';
    const PRIVATE = 'private';
    const COMMENT_EDITOR_BRIEF_COUNT = 'comment_editor_brief_count';
    const COMMENT_EDITOR_WORD_COUNT = 'comment_editor_word_count';
    const POST_EDITOR_WORD_COUNT = 'post_editor_word_count';
    const POST_EDITOR_BRIEF_COUNT = 'post_editor_brief_count';
    // 查询模式
    const QUERY_TYPE_DB_QUERY = 'db_query';  // 支持join配置的查询
    const QUERY_TYPE_SQL_QUERY = 'sql_query'; // 原生SQL查询
    const OBJECT_DEFAIL = 1;
    const OBJECT_SUCCESS = 2;

    //不受特殊规则影响的api
    const URI_NOT_IN_RULE = [
        '/api/fresns/editor/publish',
        '/api/fresns/editor/submit',
    ];
}
