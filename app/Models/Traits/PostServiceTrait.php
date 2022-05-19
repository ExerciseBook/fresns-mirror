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
use App\Helpers\StrHelper;
use Illuminate\Support\Str;

trait PostServiceTrait
{
    public function getPostInfo(string $langTag = '', string $timezone = '', string $type)
    {
        $postData = $this;
        $appendData = $this->postAppend;

        $contentLength = Str::length($postData->content);
        $briefLength = ConfigHelper::fresnsConfigByItemKey('post_editor_brief_length');
        $allowProportion = intval($appendData->allow_proportion) / 100;
        $allowLength = intval($contentLength * $allowProportion);

        if ($appendData->is_allow) {
            $allowContent = Str::limit($postData->content, $allowLength);
        } else {
            $allowContent = $postData->content;
        }

        $content = $allowContent;
        $isBrief = false;
        if ($type == 'list' && $allowLength > $briefLength) {
            $content = Str::limit($allowContent, $briefLength);
            $isBrief = true;
        }

        $info['pid'] = $postData->pid;
        $info['types'] = StrHelper::commaStringToArray($postData->types);
        $info['title'] = $postData->title;
        $info['content'] = $content;
        $info['langTag'] = $postData->lang_tag;
        $info['writingDirection'] = $postData->writing_direction;
        $info['isBrief'] = $isBrief;
        $info['isMarkdown'] = (bool) $postData->is_markdown;
        $info['isAnonymous'] = (bool) $postData->is_anonymous;
        $info['sticky'] = $postData->sticky_state;
        $info['digest'] = $postData->digest_state;
        $info['likeCount'] = $postData->like_count;
        $info['followCount'] = $postData->follow_count;
        $info['blockCount'] = $postData->block_count;
        $info['commentCount'] = $postData->comment_count;
        $info['commentLikeCount'] = $postData->comment_like_count;
        $info['time'] = DateHelper::fresnsFormatDateTime($postData->created_at, $timezone, $langTag);
        $info['timeFormat'] = DateHelper::fresnsFormatTime($postData->created_at, $langTag);
        $info['editTime'] = DateHelper::fresnsFormatDateTime($postData->latest_edit_at, $timezone, $langTag);
        $info['editTimeFormat'] = DateHelper::fresnsFormatTime($postData->latest_edit_at, $langTag);
        $info['editCount'] = $appendData->edit_count;

        $info['isAllow'] = (bool) $appendData->is_allow;
        $info['allowProportion'] = $appendData->allow_proportion.'%';
        $info['allowBtnName'] = LanguageHelper::fresnsLanguageByTableId('post_appends', 'allow_btn_name', $appendData->post_id, $langTag);
        $info['allowBtnUrl'] = ! empty($appendData->allow_plugin_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($appendData->allow_plugin_unikey) : null;

        $info['isUserList'] = (bool) $appendData->is_user_list;
        $info['userListName'] = LanguageHelper::fresnsLanguageByTableId('post_appends', 'user_list_name', $appendData->post_id, $langTag);
        $info['userListUrl'] = ! empty($appendData->user_list_plugin_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($appendData->user_list_plugin_unikey) : null;

        $location['isLbs'] = ! empty($postData->map_id) ? true : false;
        $location['mapId'] = $postData->map_id;
        $location['latitude'] = $postData->map_latitude;
        $location['longitude'] = $postData->map_longitude;
        $location['scale'] = $appendData->map_scale;
        $location['poi'] = $appendData->map_poi;
        $location['poiId'] = $appendData->map_poi_id;
        $location['distance'] = null;

        $info['location'] = $location;

        return $info;
    }

    public function getEditStatus(int $userId)
    {
        $postData = $this;
        $editConfig = ConfigHelper::fresnsConfigByItemKeys([
            'post_edit',
            'post_edit_timelimit',
            'post_edit_sticky',
            'post_edit_digest',
        ]);

        $canEdit = false;

        $editStatus['isMe'] = $postData->user_id == $userId ? true : false;
        $editStatus['canEdit'] = $canEdit;
        $editStatus['isPluginEditor'] = (bool) $postData->postAppend->is_plugin_editor;
        $editStatus['editorUrl'] = ! empty($postData->postAppend->editor_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($postData->postAppend->editor_unikey) : null;
        $editStatus['canDelete'] = (bool) $postData->postAppend->can_delete;

        return $editStatus;
    }
}
