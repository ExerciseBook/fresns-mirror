<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use App\Helpers\DateHelper;
use App\Helpers\PluginHelper;
use App\Models\PostAppend;

trait PostServiceTrait
{
    public function getPostInfo(string $langTag = '', string $timezone = '')
    {
        $postData = $this;

        $info['pid'] = $postData->pid;
        $info['types'] = $postData->types;
        $info['title'] = $postData->title;
        $info['content'] = $postData->content;
        $info['isBrief'] = $postData->is_brief;
        $info['isMarkdown'] = $postData->is_markdown;
        $info['isAnonymous'] = $postData->is_anonymous;
        $info['isLbs'] = $postData->is_lbs;
        $info['isAllow'] = $postData->is_allow;
        $info['sticky'] = $postData->sticky_state;
        $info['digest'] = $postData->digest_state;
        $info['viewCount'] = $postData->view_count;
        $info['likeCount'] = $postData->like_count;
        $info['followCount'] = $postData->follow_count;
        $info['blockCount'] = $postData->block_count;
        $info['commentCount'] = $postData->comment_count;
        $info['commentLikeCount'] = $postData->comment_like_count;
        $info['time'] = DateHelper::fresnsFormatDateTime($postData->created_at, $timezone, $langTag);
        $info['timeFormat'] = DateHelper::fresnsFormatTime($postData->created_at, $langTag);
        $info['editTime'] = DateHelper::fresnsFormatDateTime($postData->latest_edit_at, $timezone, $langTag);
        $info['editTimeFormat'] = DateHelper::fresnsFormatTime($postData->latest_edit_at, $langTag);

        return $info;
    }

    public function getPostAppendInfo(string $langTag = '', string $timezone = '')
    {
        $postData = $this;

        $postAppendData = PostAppend::where('post_id', $postData->id)->first();

        $info['platformId'] = $postAppendData->platform_id;
        $info['isPluginEditor'] = $postAppendData->is_plugin_editor;
        $info['editorUrl'] = PluginHelper::fresnsPluginUrlByUnikey($postAppendData->editor_unikey);
        $info['canDelete'] = $postAppendData->can_delete;

        return $info;
    }
}
