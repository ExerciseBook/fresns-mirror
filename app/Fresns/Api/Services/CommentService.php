<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Services;

use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\InteractiveHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;
use App\Models\ArchiveUsage;
use App\Models\Comment;
use App\Models\CommentLog;
use App\Models\ExtendUsage;
use App\Models\Mention;
use App\Models\OperationUsage;
use App\Models\PluginUsage;
use App\Utilities\ContentUtility;
use App\Utilities\ExtendUtility;
use App\Utilities\InteractiveUtility;
use App\Utilities\LbsUtility;
use App\Utilities\PermissionUtility;
use Illuminate\Support\Str;

class CommentService
{
    public function commentList(?Comment $comment, string $langTag, string $timezone, ?int $authUserId = null)
    {
        if (! $comment) {
            return null;
        }

        $commentInfo = $comment->getCommentInfo($langTag, $timezone);
        $contentHandle = self::contentHandle($comment, 'list', $authUserId);

        $item['operations'] = ExtendUtility::getOperations(OperationUsage::TYPE_COMMENT, $comment->id, $langTag);

        $item['hashtags'] = null;
        if ($comment->hashtags->isNotEmpty()) {
            $hashtagService = new HashtagService;

            foreach ($comment->hashtags as $hashtag) {
                $hashtagItem[] = $hashtagService->hashtagList($hashtag, $langTag, $authUserId);
            }
            $item['hashtags'] = $hashtagItem;
        }

        $item['creator'] = InteractiveHelper::fresnsUserAnonymousProfile();
        if (! $comment->is_anonymous) {
            $creatorProfile = $comment->creator->getUserProfile($langTag, $timezone);
            $creatorMainRole = $comment->creator->getUserMainRole($langTag, $timezone);
            $creatorOperations = ExtendUtility::getOperations(OperationUsage::TYPE_USER, $post->creator->id, $langTag);
            $item['creator'] = array_merge($creatorProfile, $creatorMainRole, $creatorOperations);
        }

        $info = array_merge($commentInfo, $contentHandle, $item);

        return $info;
    }

    public function commentDetail(Comment $comment, string $type, string $langTag, string $timezone, ?int $authUserId = null, ?int $mapId = null, ?string $authUserLng = null, ?string $authUserLat = null)
    {
        $commentInfo = $comment->getCommentInfo($langTag, $timezone);
        $commentAppend = $comment->commentAppend;
        $postAppend = $comment->postAppend;

        $contentHandle = self::contentHandle($comment, $type, $authUserId);

        if (! empty($comment->map_id) && ! empty($authUserLng) && ! empty($authUserLat)) {
            $postLng = $comment->map_longitude;
            $postLat = $comment->map_latitude;
            $commentInfo['location']['distance'] = LbsUtility::getDistanceWithUnit($langTag, $postLng, $postLat, $authUserLng, $authUserLat);
        }

        $item['archives'] = ExtendUtility::getArchives(ArchiveUsage::TYPE_COMMENT, $comment->id, $langTag);
        $item['operations'] = ExtendUtility::getOperations(OperationUsage::TYPE_COMMENT, $comment->id, $langTag);
        $item['extends'] = ExtendUtility::getExtends(ExtendUsage::TYPE_COMMENT, $comment->id, $langTag);
        $item['files'] = FileHelper::fresnsAntiLinkFileInfoListByTableColumn('comments', 'id', $comment->id);

        $fileCount['images'] = collect($item['files']['images'])->count();
        $fileCount['videos'] = collect($item['files']['videos'])->count();
        $fileCount['audios'] = collect($item['files']['audios'])->count();
        $fileCount['documents'] = collect($item['files']['documents'])->count();
        $item['fileCount'] = $fileCount;

        $item['hashtags'] = null;
        if ($comment->hashtags->isNotEmpty()) {
            $hashtagService = new HashtagService;

            foreach ($comment->hashtags as $hashtag) {
                $hashtagItem[] = $hashtagService->hashtagList($hashtag, $langTag, $authUserId);
            }
            $item['hashtags'] = $hashtagItem;
        }

        $item['creator'] = InteractiveHelper::fresnsUserAnonymousProfile();
        if (! $comment->is_anonymous) {
            $creatorProfile = $comment->creator->getUserProfile($langTag, $timezone);
            $creatorMainRole = $comment->creator->getUserMainRole($langTag, $timezone);
            $creatorOperations = ExtendUtility::getOperations(OperationUsage::TYPE_USER, $comment->creator->id, $langTag);
            $item['creator'] = array_merge($creatorProfile, $creatorMainRole, $creatorOperations);
        }

        $item['commentPreviews'] = null;

        $previewConfig = ConfigHelper::fresnsConfigByItemKey('comment_preview');
        if ($type == 'list' && $previewConfig != 0) {
            $comments = Comment::with('creator')
                ->where('parent_id', $comment->id)
                ->orderByDesc('like_count')
                ->limit($previewConfig)
                ->get();

            $commentList = null;
            $service = new CommentService();

            /** @var Comment $comment */
            foreach ($comments as $comment) {
                $commentList[] = $service->commentList($comment, $langTag, $timezone, $authUserId);
            }

            $item['commentPreviews'] = $commentList;
        }

        $isMe = $comment->user_id == $authUserId ? true : false;

        $commentBtn['status'] = false;
        $commentBtn['name'] = null;
        $commentBtn['url'] = null;
        $commentBtn['style'] = null;

        if ($isMe && $commentAppend->is_close_btn) {
            $commentBtn['status'] = true;
            if ($commentAppend->is_change_btn) {
                $commentBtn['name'] = LanguageHelper::fresnsLanguageByTableId('posts', 'comment_btn_name', $postAppend->post_id, $langTag);
                $commentBtn['style'] = $postAppend->comment_btn_style;
            } else {
                $commentBtn['name'] = ConfigHelper::fresnsConfigByItemKey($commentAppend->btn_name_key, $langTag);
                $commentBtn['style'] = $commentAppend->btn_style;
            }
            $editStatus['url'] = ! empty($postAppend->comment_btn_plugin_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($postAppend->comment_btn_plugin_unikey) : null;
        }

        $item['commentBtn'] = $commentBtn;

        $item['manages'] = ExtendUtility::getPluginUsages(PluginUsage::TYPE_MANAGE, null, PluginUsage::SCENE_COMMENT, $authUserId, $langTag);

        $editStatus['isMe'] = false;
        $editStatus['canDelete'] = false;
        $editStatus['canEdit'] = false;
        $editStatus['isPluginEditor'] = false;
        $editStatus['editorUrl'] = null;

        if ($isMe) {
            $editStatus['isMe'] = true;
            $editStatus['canDelete'] = (bool) $commentAppend->can_delete;
            $editStatus['canEdit'] = PermissionUtility::checkContentIsCanEdit('comment', $comment->created_at, $comment->sticky_state, $comment->digest_state, $langTag, $timezone);
            $editStatus['isPluginEditor'] = (bool) $commentAppend->is_plugin_editor;
            $editStatus['editorUrl'] = ! empty($commentAppend->editor_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($commentAppend->editor_unikey) : null;
        }
        $item['editStatus'] = $editStatus;

        $interactiveConfig = InteractiveHelper::fresnsCommentInteractive($langTag);
        $interactiveStatus = InteractiveUtility::checkInteractiveStatus(InteractiveUtility::TYPE_COMMENT, $comment->id, $authUserId);
        $item['interactive'] = array_merge($interactiveConfig, $interactiveStatus);

        $detail = array_merge($commentInfo, $contentHandle, $item);

        return $detail;
    }

    public static function contentHandle(Comment $comment, string $type, ?int $authUserId = null)
    {
        $postAppend = $comment->postAppend;

        $contentLength = Str::length($comment->content);

        $briefLength = ConfigHelper::fresnsConfigByItemKey('comment_editor_brief_length');

        $item['contentPublic'] = (bool) $postAppend->is_comment_public;
        if (! $item['contentPublic']) {
            $commentInfo['content'] = null;
        } elseif ($type == 'list' && $contentLength > $briefLength) {
            $commentInfo['content'] = Str::limit($comment->content, $briefLength);
            $commentInfo['isBrief'] = true;
        } else {
            $commentInfo['content'] = $comment->content;
        }

        $commentInfo['content'] = ContentUtility::handleAndReplaceAll($commentInfo['content'], $comment->is_markdown, Mention::TYPE_COMMENT, $authUserId);

        return $commentInfo;
    }

    // comment Log
    public function commentLogList(CommentLog $log, string $langTag, string $timezone, ?int $authUserId = null)
    {
        return null;
    }

    // comment log detail
    public function commentLogDetail(CommentLog $log, string $langTag, string $timezone, ?int $authUserId = null)
    {
        return null;
    }
}
