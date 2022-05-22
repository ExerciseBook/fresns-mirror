<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use App\Helpers\ConfigHelper;
use App\Helpers\DateHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;

trait CommentServiceTrait
{
    public function getCommentInfo(string $langTag = '', string $timezone = '', string $type)
    {
        $commentData = $this;
        $appendData = $this->commentAppend;
        $postAppendData = $this->post->postAppend;

        $info['cid'] = $commentData->cid;
        $info['types'] = explode(',', $commentData->types);
        $info['title'] = $commentData->title;
        $info['content'] = $commentData->content;
        $info['contentLength'] = Str::length($commentData->content);
        $info['langTag'] = $commentData->lang_tag;
        $info['writingDirection'] = $commentData->writing_direction;
        $info['isBrief'] = false;
        $info['isMarkdown'] = (bool) $commentData->is_markdown;
        $info['isAnonymous'] = (bool) $commentData->is_anonymous;
        $info['isSticky'] = $commentData->is_sticky;
        $info['digestState'] = $commentData->digest_state;
        $info['ipRegion'] = $appendData->ip_region;
        $info['likeCount'] = $commentData->like_count;
        $info['followCount'] = $commentData->follow_count;
        $info['blockCount'] = $commentData->block_count;
        $info['commentCount'] = $commentData->comment_count;
        $info['commentLikeCount'] = $commentData->comment_like_count;
        $info['createTime'] = DateHelper::fresnsFormatDateTime($commentData->created_at, $timezone, $langTag);
        $info['createTimeFormat'] = DateHelper::fresnsFormatTime($commentData->created_at, $langTag);
        $info['editTime'] = DateHelper::fresnsFormatDateTime($commentData->latest_edit_at, $timezone, $langTag);
        $info['editTimeFormat'] = DateHelper::fresnsFormatTime($commentData->latest_edit_at, $langTag);
        $info['editCount'] = $appendData->edit_count;

        $info['isCommentBtn'] = (bool) $postAppendData->is_comment_btn;
        $info['commentBtnName'] = LanguageHelper::fresnsLanguageByTableId('post_appends', 'comment_btn_name', $commentData->post_id, $langTag);
        $info['commentBtnUrl'] = ! empty($postAppendData->comment_btn_plugin_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($postAppendData->comment_btn_plugin_unikey) : null;

        $location['isLbs'] = ! empty($commentData->map_id) ? true : false;
        $location['mapId'] = $commentData->map_id;
        $location['latitude'] = $commentData->map_latitude;
        $location['longitude'] = $commentData->map_longitude;
        $location['scale'] = $appendData->map_scale;
        $location['poi'] = $appendData->map_poi;
        $location['poiId'] = $appendData->map_poi_id;
        $location['distance'] = null;

        $info['location'] = $location;

        return $info;
    }

    public function getEditStatus(int $userId)
    {
        $commentData = $this;
        $editConfig = ConfigHelper::fresnsConfigByItemKeys([
            'comment_edit',
            'comment_edit_timelimit',
            'comment_edit_sticky',
            'comment_edit_digest',
        ]);

        $canEdit = false;

        $editStatus['isMe'] = $commentData->user_id == $userId ? true : false;
        $editStatus['canEdit'] = $canEdit;
        $editStatus['isPluginEditor'] = (bool) $commentData->commentAppend->is_plugin_editor;
        $editStatus['editorUrl'] = ! empty($commentData->commentAppend->editor_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($commentData->commentAppend->editor_unikey) : null;
        $editStatus['canDelete'] = (bool) $commentData->commentAppend->can_delete;

        return $editStatus;
    }
}
