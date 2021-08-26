<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Member;

use App\Http\Fresns\FresnsApi\Base\FresnsBaseChecker;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShields;

//业务检查，比如状态
class AmChecker extends FresnsBaseChecker
{
    /**
     * 校验点赞，关注，屏蔽是否有添加
     * markType 1.点赞 2.关注 3.屏蔽
     * markTarget 1.成员 / 2.小组 / 3.话题 / 4.帖子 / 5.评论.
     */
    public static function checkMark($markType, $markTarget, $memberId, $toMemberId)
    {
        switch ($markType) {
            case 1:
                $likeCount = FresnsMemberLikes::where('member_id', $memberId)->where('like_type',
                    $markTarget)->where('like_id', $toMemberId)->count();
                if ($likeCount > 0) {
                    return true;
                }
                break;
            case 2:
                $followCount = FresnsMemberFollows::where('member_id', $memberId)->where('follow_type',
                    $markTarget)->where('follow_id', $toMemberId)->count();
                if ($followCount > 0) {
                    return true;
                }
                break;
            default:
                $shieldCount = FresnsMemberShields::where('member_id', $memberId)->where('shield_type',
                    $markTarget)->where('shield_id', $toMemberId)->count();
                if ($shieldCount > 0) {
                    return true;
                }
                break;
        }

        return false;
    }

    /**
     * 是否有权操作，根据配置表设置配置键名 > 运营配置 > 互动配置 > 互动行为设置，设置为 false 时，不可操作。
     * markType 1.点赞 2.关注 3.屏蔽
     * markTarget 1.成员 / 2.小组 / 3.话题 / 4.帖子 / 5.评论.
     */
    public static function checkMarkApi($markType, $markTarget)
    {
        switch ($markType) {
            case 1:
                switch ($markTarget) {
                    case 1:
                        $isMark = ApiConfigHelper::getConfigByItemKey('like_member_setting');
                        break;
                    case 2:
                        $isMark = ApiConfigHelper::getConfigByItemKey('like_group_setting');
                        break;
                    case 3:
                        $isMark = ApiConfigHelper::getConfigByItemKey('like_hashtag_setting');
                        break;
                    case 4:
                        $isMark = ApiConfigHelper::getConfigByItemKey('like_post_setting');
                        break;
                    default:
                        $isMark = ApiConfigHelper::getConfigByItemKey('like_comment_setting');
                        break;
                }
                break;
            case 2:
                switch ($markTarget) {
                    case 1:
                        $isMark = ApiConfigHelper::getConfigByItemKey('follow_member_setting');
                        break;
                    case 2:
                        $isMark = ApiConfigHelper::getConfigByItemKey('follow_group_setting');
                        break;
                    case 3:
                        $isMark = ApiConfigHelper::getConfigByItemKey('follow_hashtag_setting');
                        break;
                    case 4:
                        $isMark = ApiConfigHelper::getConfigByItemKey('follow_post_setting');
                        break;
                    default:
                        $isMark = ApiConfigHelper::getConfigByItemKey('follow_comment_setting');
                        break;
                }
                break;

            default:
                switch ($markTarget) {
                    case 1:
                        $isMark = ApiConfigHelper::getConfigByItemKey('shield_member_setting');
                        break;
                    case 2:
                        $isMark = ApiConfigHelper::getConfigByItemKey('shield_group_setting');
                        break;
                    case 3:
                        $isMark = ApiConfigHelper::getConfigByItemKey('shield_hashtag_setting');
                        break;
                    case 4:
                        $isMark = ApiConfigHelper::getConfigByItemKey('shield_post_setting');
                        break;
                    default:
                        $isMark = ApiConfigHelper::getConfigByItemKey('shield_comment_setting');
                        break;
                }
                break;
        }

        return $isMark;
    }

    /**
     * 查看别人信息时，是否输出数据，根据配置表设置配置键名 > 运营配置 > 互动配置 > 查看别人内容设置，设置为 false 时，不输出数据。
     * viewType 1.点赞 2.关注 3.屏蔽
     * viewTarget 1.成员 / 2.小组 / 3.话题 / 4.帖子 / 5.评论.
     */
    public static function checkMarkLists($viewType, $viewTarget)
    {
        switch ($viewType) {
            case 1:
                switch ($viewTarget) {
                    case 1:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_like_members');
                        break;
                    case 2:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_like_groups');
                        break;
                    case 3:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_like_hashtags');
                        break;
                    case 4:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_like_posts');
                        break;
                    default:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_like_comments');
                        break;
                }
                break;
            case 2:
                switch ($viewTarget) {
                    case 1:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_follow_members');
                        break;
                    case 2:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_follow_groups');
                        break;
                    case 3:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_follow_hashtags');
                        break;
                    case 4:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_follow_posts');
                        break;
                    default:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_follow_comments');
                        break;
                }
                break;

            default:
                switch ($viewTarget) {
                    case 1:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_shield_members');
                        break;
                    case 2:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_shield_groups');
                        break;
                    case 3:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_shield_hashtags	');
                        break;
                    case 4:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_shield_posts');
                        break;
                    default:
                        $isMark = ApiConfigHelper::getConfigByItemKey('it_shield_comments');
                        break;
                }
                break;
        }

        return $isMark;
    }
}
