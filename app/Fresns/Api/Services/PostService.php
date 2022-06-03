<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Services;

use App\Helpers\AppHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\FileHelper;
use App\Helpers\PluginHelper;
use App\Helpers\InteractiveHelper;
use App\Models\Post;
use App\Models\User;
use App\Utilities\ExtendUtility;
use App\Utilities\LbsUtility;
use App\Utilities\PermissionUtility;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PostService
{
    public function postDetail(Post $post, string $type, ?int $mapId = null, ?string $userLng = null, ?string $userLat = null, ?Collection $hashtags = null)
    {
        $headers = AppHelper::getApiHeaders();
        $user = ! empty($headers['uid']) ? User::whereUid($headers['uid'])->first() : null;

        $postInfo = $post->getPostInfo($headers['langTag'], $headers['timezone']);

        if ($postInfo['isAllow']) {
            $allowProportion = intval($postInfo['allowProportion']) / 100;
            $allowLength = intval($postInfo['contentLength'] * $allowProportion);

            if (empty($user->id)) {
                $content = Str::limit($postInfo['content'], $allowLength);
            } else {
                $checkPostAllow = PermissionUtility::checkPostAllow($user->id, $post->id);
                if (! $checkPostAllow) {
                    $content = Str::limit($postInfo['content'], $allowLength);
                } else {
                    $content = $postInfo['content'];
                    $postInfo['isAllow'] = false;
                }
            }
        } else {
            $content = $postInfo['content'];
        }

        $contentLength = Str::length($content);
        $briefLength = ConfigHelper::fresnsConfigByItemKey('post_editor_brief_length');

        if ($type == 'list' && $contentLength > $briefLength) {
            $postInfo['content'] = Str::limit($content, $briefLength);
            $postInfo['isBrief'] = true;
        } else {
            $postInfo['content'] = $content;
        }

        if (! empty($post->map_id)) {
            $postLng = $post->map_longitude;
            $postLat = $post->map_latitude;
            if (! empty($userLat) && ! empty($userLon)) {
                $postInfo['location']['distance'] = LbsUtility::getDistanceWithUnit($headers['langTag'], $postLng, $postLat, $userLng, $userLat);
            }
        }

        $item['files'] = FileHelper::fresnsAntiLinkFileInfoListByTableColumn('posts', 'id', $post->id);
        $item['extends'] = ExtendUtility::getExtends(4, $post->id, $headers['langTag']);
        $item['icons'] = ExtendUtility::getIcons(4, $post->id, $headers['langTag']);
        $item['tips'] = ExtendUtility::getTips(4, $post->id, $headers['langTag']);

        $attachCount['images'] = collect($item['files']['images'])->count();
        $attachCount['videos'] = collect($item['files']['videos'])->count();
        $attachCount['audios'] = collect($item['files']['audios'])->count();
        $attachCount['documents'] = collect($item['files']['documents'])->count();
        $attachCount['icons'] = collect($item['icons'])->count();
        $attachCount['tips'] = collect($item['tips'])->count();
        $attachCount['extends'] = collect($item['extends'])->count();
        $item['attachCount'] = $attachCount;

        $item['group'] = $post->group?->getGroupInfo($headers['langTag']);
        $item['hashtags'] = null;
        if ($hashtags) {
            foreach ($hashtags as $hashtag) {
                $hashtagItem['hid'] = $hashtag->slug;
                $hashtagItem['hname'] = $hashtag->name;
                $hashtagItem['description'] = $hashtag->description;
                $hashtagItem['cover'] = FileHelper::fresnsFileUrlByTableColumn($hashtag->cover_file_id, $hashtag->cover_file_url);
                $hashtagItem['likeCount'] = $hashtag->like_count;
                $hashtagItem['dislikeCount'] = $hashtag->dislike_count;
                $hashtagItem['followCount'] = $hashtag->follow_count;
                $hashtagItem['postCount'] = $hashtag->post_count;
                $hashtagItem['commentCount'] = $hashtag->comment_count;
                $hashtagItem['digestCount'] = $hashtag->digest_count;
                $item['hashtag'][] = $hashtagItem;
            }
        }

        $item['creator'] = InteractiveHelper::fresnsUserAnonymousProfile();
        if (! $post->is_anonymous) {
            $creatorProfile = $post->creator->getUserProfile($headers['langTag'], $headers['timezone']);
            $creatorMainRole = $post->creator->getUserMainRole($headers['langTag'], $headers['timezone']);
            $item['creator'] = array_merge($creatorProfile, $creatorMainRole);
        }

        $item['manages'] = ExtendUtility::getPluginExtends(5, $post->group_id, 1, $user?->id, $headers['langTag']);

        $editStatus['isMe'] = false;
        $editStatus['canDelete'] = false;
        $editStatus['canEdit'] = false;
        $editStatus['isPluginEditor'] = false;
        $editStatus['editorUrl'] = null;

        $isMe = $post->user_id == $user?->id ? true : false;
        if ($isMe) {
            $editStatus['isMe'] = true;
            $editStatus['canDelete'] = (bool) $post->postAppend->can_delete;
            $editStatus['canEdit'] = self::isCanEdit($post->created_at, $post->sticky_state, $post->digest_state);
            $editStatus['isPluginEditor'] = (bool) $post->postAppend->is_plugin_editor;
            $editStatus['editorUrl'] = ! empty($post->postAppend->editor_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($post->postAppend->editor_unikey) : null;
        }
        $item['editStatus'] = $editStatus;

        $postInteractive = InteractiveHelper::fresnsPostInteractive($headers['langTag']);

        $detail = array_merge($postInfo, $item, $postInteractive);

        return $detail;
    }

    public static function isCanEdit(string $createTime, int $stickyState, int $digestState): bool
    {
        $editConfig = ConfigHelper::fresnsConfigByItemKeys([
            'post_edit',
            'post_edit_timelimit',
            'post_edit_sticky',
            'post_edit_digest',
        ]);

        if (! $editConfig['post_edit']) {
            return false;
        }

        return false;
    }
}
