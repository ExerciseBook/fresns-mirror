<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Models\Comment;
use App\Models\Domain;
use App\Models\DomainLink;
use App\Models\DomainLinkLinked;
use App\Models\Group;
use App\Models\Hashtag;
use App\Models\Post;
use App\Models\UserBlock;
use App\Models\UserFollow;
use App\Models\UserLike;
use App\Models\UserStat;

class InteractiveUtility
{
    const TYPE_USER = 1;
    const TYPE_GROUP = 2;
    const TYPE_HASHTAG = 3;
    const TYPE_POST = 4;
    const TYPE_COMMENT = 5;

    // check interactive
    public static function checkUserLike(int $likeType, int $likeId, ?int $userId = null): bool
    {
        if (empty($userId)) {
            return false;
        }

        $checkLike = UserLike::where('user_id', $userId)
            ->markType(UserLike::MARK_TYPE_LIKE)
            ->type($likeType)
            ->where('like_id', $likeId)
            ->first();

        return (bool) $checkLike;
    }

    public static function checkUserDislike(int $dislikeType, int $dislikeId, ?int $userId = null): bool
    {
        if (empty($userId)) {
            return false;
        }

        $checkDislike = UserLike::where('user_id', $userId)
            ->markType(UserLike::MARK_TYPE_DISLIKE)
            ->type($dislikeType)
            ->where('like_id', $dislikeId)
            ->first();

        return (bool) $checkDislike;
    }

    public static function checkUserFollow(int $followType, int $followId, ?int $userId = null): bool
    {
        if (empty($userId)) {
            return false;
        }

        $checkFollow = UserFollow::where('user_id', $userId)
            ->type($followType)
            ->where('follow_id', $followId)
            ->first();

        return (bool) $checkFollow;
    }

    public static function checkUserBlock(int $blockType, int $blockId, ?int $userId = null): bool
    {
        if (empty($userId)) {
            return false;
        }

        $checkBlock = UserBlock::where('user_id', $userId)
            ->type($blockType)
            ->where('block_id', $blockId)
            ->first();

        return (bool) $checkBlock;
    }

    public static function checkInteractiveStatus(int $markType, int $markId, ?int $userId = null): array
    {
        if (empty($userId)) {
            $status['likeStatus'] = false;
            $status['dislikeStatus'] = false;
            $status['followStatus'] = false;
            $status['blockStatus'] = false;

            return $status;
        }

        $status['likeStatus'] = self::checkUserLike($markType, $markId, $userId);
        $status['dislikeStatus'] = self::checkUserDislike($markType, $markId, $userId);
        $status['followStatus'] = self::checkUserFollow($markType, $markId, $userId);
        $status['blockStatus'] = self::checkUserBlock($markType, $markId, $userId);

        return $status;
    }

    // check follow me
    public static function checkUserFollowMe(int $userId, ?int $myUserId = null): bool
    {
        if (empty($userId)) {
            return false;
        }

        if ($userId == $myUserId) {
            return false;
        }

        $checkFollowMe = UserFollow::where('user_id', $userId)
            ->type(UserFollow::TYPE_USER)
            ->where('follow_id', $myUserId)
            ->first();

        return (bool) $checkFollowMe;
    }

    // mark interactive
    public static function markUserLike(int $userId, int $likeType, int $likeId)
    {
        $userLike = UserLike::withTrashed()
            ->where('user_id', $userId)
            ->type($likeType)
            ->where('like_id', $likeId)
            ->first();

        if ($userLike->trashed() || empty($userLike)) {
            if ($userLike->trashed() && $userLike->mark_type == UserLike::MARK_TYPE_LIKE) {
                // trashed data, mark type=like
                $userLike->restore();

                InteractiveUtility::markStats($userId, 'like', $likeType, $likeId, 'increment');
            } elseif ($userLike->trashed() && $userLike->mark_type == UserLike::MARK_TYPE_DISLIKE) {
                // trashed data, mark type=dislike
                $userLike->restore();

                $userLike->update([
                    'mark_type' => UserLike::MARK_TYPE_LIKE,
                ]);

                InteractiveUtility::markStats($userId, 'like', $likeType, $likeId, 'increment');
                InteractiveUtility::markStats($userId, 'dislike', $likeType, $likeId, 'decrement');
            } else {
                // like null
                UserLike::updateOrCreate([
                    'user_id' => $userId,
                    'like_type' => $likeType,
                    'like_id' => $likeId,
                ], [
                    'mark_type' => UserLike::MARK_TYPE_LIKE,
                ]);

                InteractiveUtility::markStats($userId, 'like', $likeType, $likeId, 'increment');
            }
        } else {
            if ($userLike->mark_type == UserLike::MARK_TYPE_LIKE) {
                // documented, mark type=like
                $userLike->delete();

                InteractiveUtility::markStats($userId, 'like', $likeType, $likeId, 'decrement');
            } else {
                // documented, mark type=dislike
                $userLike->update([
                    'mark_type' => UserLike::MARK_TYPE_LIKE,
                ]);

                InteractiveUtility::markStats($userId, 'like', $likeType, $likeId, 'increment');
                InteractiveUtility::markStats($userId, 'dislike', $likeType, $likeId, 'decrement');
            }
        }

        return;
    }

    public static function markUserDislike(int $userId, int $dislikeType, int $dislikeId)
    {
        $userDislike = UserLike::withTrashed()
            ->where('user_id', $userId)
            ->type($dislikeType)
            ->where('like_id', $dislikeId)
            ->first();

        if ($userDislike->trashed() || empty($userDislike)) {
            if ($userDislike->trashed() && $userDislike->mark_type == UserLike::MARK_TYPE_DISLIKE) {
                // trashed data, mark type=dislike
                $userDislike->restore();

                InteractiveUtility::markStats($userId, 'dislike', $dislikeType, $dislikeId, 'increment');
            } elseif ($userDislike->trashed() && $userDislike->mark_type == UserLike::MARK_TYPE_LIKE) {
                // trashed data, mark type=like
                $userDislike->restore();

                $userDislike->update([
                    'mark_type' => UserLike::MARK_TYPE_DISLIKE,
                ]);

                InteractiveUtility::markStats($userId, 'dislike', $dislikeType, $dislikeId, 'increment');
                InteractiveUtility::markStats($userId, 'like', $dislikeType, $dislikeId, 'decrement');
            } else {
                // dislike null
                UserLike::updateOrCreate([
                    'user_id' => $userId,
                    'like_type' => $dislikeType,
                    'like_id' => $dislikeId,
                ], [
                    'mark_type' => UserLike::MARK_TYPE_DISLIKE,
                ]);

                InteractiveUtility::markStats($userId, 'dislike', $dislikeType, $dislikeId, 'increment');
            }
        } else {
            if ($userDislike->mark_type == UserLike::MARK_TYPE_DISLIKE) {
                // documented, mark type=dislike
                $userDislike->delete();

                InteractiveUtility::markStats($userId, 'dislike', $dislikeType, $dislikeId, 'decrement');
            } else {
                // documented, mark type=like
                $userDislike->update([
                    'mark_type' => UserLike::MARK_TYPE_DISLIKE,
                ]);

                InteractiveUtility::markStats($userId, 'dislike', $dislikeType, $dislikeId, 'increment');
                InteractiveUtility::markStats($userId, 'like', $dislikeType, $dislikeId, 'decrement');
            }
        }

        return;
    }

    public static function markUserFollow(int $userId, int $followType, int $followId)
    {
        $userFollow = UserFollow::withTrashed()
            ->where('user_id', $userId)
            ->type($followType)
            ->where('follow_id', $followId)
            ->first();

        if ($userFollow->trashed() || empty($userFollow)) {
            if ($userFollow->trashed()) {
                // trashed data
                $userFollow->restore();

                InteractiveUtility::markStats($userId, 'follow', $followType, $followId, 'increment');
            } else {
                // dislike null
                UserFollow::updateOrCreate([
                    'user_id' => $userId,
                    'follow_type' => $followType,
                    'follow_id' => $followId,
                ]);

                InteractiveUtility::markStats($userId, 'follow', $followType, $followId, 'increment');
            }
        } else {
            $userFollow->delete();

            InteractiveUtility::markStats($userId, 'follow', $followType, $followId, 'decrement');
        }

        // is_mutual
        if ($followType == UserFollow::TYPE_USER) {
            $myFollow = UserFollow::where('user_id', $userId)->type(UserFollow::TYPE_USER)->where('follow_id', $followId)->first();
            $itFollow = UserFollow::where('user_id', $followId)->type(UserFollow::TYPE_USER)->where('follow_id', $userId)->first();

            if (! empty($myFollow) && ! empty($itFollow)) {
                $myFollow->update(['is_mutual' => 1]);
                $itFollow->update(['is_mutual' => 1]);
            } else {
                $myFollow->update(['is_mutual' => 0]);
                $itFollow->update(['is_mutual' => 0]);
            }
        }

        return;
    }

    public static function markUserBlock(int $userId, int $blockType, int $blockId)
    {
        $userBlock = UserBlock::withTrashed()
            ->where('user_id', $userId)
            ->type($blockType)
            ->where('block_id', $blockId)
            ->first();

        if ($userBlock->trashed() || empty($userBlock)) {
            if ($userBlock->trashed()) {
                // trashed data
                $userBlock->restore();

                InteractiveUtility::markStats($userId, 'block', $blockType, $blockId, 'increment');
            } else {
                // dislike null
                UserBlock::updateOrCreate([
                    'user_id' => $userId,
                    'block_type' => $blockType,
                    'block_id' => $blockId,
                ]);

                InteractiveUtility::markStats($userId, 'block', $blockType, $blockId, 'increment');
            }
        } else {
            $userBlock->delete();

            InteractiveUtility::markStats($userId, 'block', $blockType, $blockId, 'decrement');
        }

        return;
    }

    // mark content sticky
    public static function markContentSticky(string $type, int $id, int $stickyState)
    {
        switch ($type) {
            // post
            case 'post':
                $post = Post::where('id', $id)->first();
                $post->update([
                    'sticky_state' => $stickyState,
                ]);
            break;

            // comment
            case 'comment':
                $comment = Comment::where('id', $id)->first();

                if ($stickyState == 1) {
                    $comment->update([
                        'is_sticky' => 1,
                    ]);
                } else {
                    $comment->update([
                        'is_sticky' => 0,
                    ]);
                }
            break;
        }

        return;
    }

    // mark content digest
    public static function markContentDigest(string $type, int $id, int $digestState)
    {
        // todo: 没有 default，如果 $digestState 值在预期之外，会报错，可忽略
        $digestStats = match ($digestState) {
            1 => 'no',
            2 => 'yes',
            3 => 'yes',
        };

        switch ($type) {
            // post
            case 'post':
                $post = Post::where('id', $id)->first();

                if ($post->digest_state == 1 && $digestStats == 'yes') {
                    $post->update([
                        'digest_state' => $digestState,
                    ]);

                    InteractiveUtility::digestStats('post', $post->id, 'increment');
                } elseif ($post->digest_state != 1 && $digestStats == 'no') {
                    $post->update([
                        'digest_state' => $digestState,
                    ]);

                    InteractiveUtility::digestStats('post', $post->id, 'decrement');
                } else {
                    $post->update([
                        'digest_state' => $digestState,
                    ]);
                }
            break;

            // comment
            case 'comment':
                $comment = Comment::where('id', $id)->first();

                if ($comment->digest_state == 1 && $digestStats == 'yes') {
                    $comment->update([
                        'digest_state' => $digestState,
                    ]);

                    InteractiveUtility::digestStats('comment', $comment->id, 'increment');
                } elseif ($comment->digest_state != 1 && $digestStats == 'no') {
                    $comment->update([
                        'digest_state' => $digestState,
                    ]);

                    InteractiveUtility::digestStats('comment', $comment->id, 'decrement');
                } else {
                    $comment->update([
                        'digest_state' => $digestState,
                    ]);
                }
            break;
        }

        return;
    }

    /**
     * It increments or decrements the stats of the user, group, hashtag, post, or comment that was
     * interacted with
     *
     * @param int userId The user who is performing the action.
     * @param string interactiveType The type of interactive action(like, dislike, follow, block).
     * @param int markType 1 = user, 2 = group, 3 = hashtag, 4 = post, 5 = comment
     * @param int markId The id of the user, group, hashtag, post, or comment that is being marked.
     * @param string actionType increment or decrement
     */
    public static function markStats(int $userId, string $interactiveType, int $markType, int $markId, string $actionType)
    {
        $tableClass = match ($markType) {
            1 => 'user',
            2 => 'group',
            3 => 'hashtag',
            4 => 'post',
            5 => 'comment',
        };

        switch ($actionType) {
            // increment
            case 'increment':
                switch ($tableClass) {
                    // user
                    case 'user':
                        UserStat::where('user_id', $userId)->increment("{$interactiveType}_user_count");
                        UserStat::where('user_id', $markId)->increment("{$interactiveType}_me_count");
                    break;

                    // group
                    case 'group':
                        UserStat::where('user_id', $userId)->increment("{$interactiveType}_group_count");
                        Group::where('id', $markId)->increment("{$interactiveType}_count");
                    break;

                    // hashtag
                    case 'hashtag':
                        UserStat::where('user_id', $userId)->increment("{$interactiveType}_group_count");
                        Hashtag::where('id', $markId)->increment("{$interactiveType}_count");
                    break;

                    // post
                    case 'post':
                        UserStat::where('user_id', $userId)->increment("{$interactiveType}_group_count");
                        $post = Post::where('id', $markId)->first();
                        $post?->increment("{$interactiveType}_count");
                        UserStat::where('user_id', $post?->user_id)->increment("post_{$interactiveType}_count");
                    break;

                    // comment
                    case 'comment':
                        UserStat::where('user_id', $userId)->increment("{$interactiveType}_group_count");

                        InteractiveUtility::markStatsComment($markId, $interactiveType, 'increment');
                    break;
                }
            break;

            // decrement
            case 'decrement':
                switch ($tableClass) {
                    // user
                    case 'user':
                        UserStat::where('user_id', $userId)->decrement("{$interactiveType}_user_count");
                        UserStat::where('user_id', $markId)->decrement("{$interactiveType}_me_count");
                    break;

                    // group
                    case 'group':
                        UserStat::where('user_id', $userId)->decrement("{$interactiveType}_group_count");
                        Group::where('id', $markId)->decrement("{$interactiveType}_count");
                    break;

                    // hashtag
                    case 'hashtag':
                        UserStat::where('user_id', $userId)->decrement("{$interactiveType}_group_count");
                        Hashtag::where('id', $markId)->decrement("{$interactiveType}_count");
                    break;

                    // post
                    case 'post':
                        UserStat::where('user_id', $userId)->decrement("{$interactiveType}_group_count");
                        $post = Post::where('id', $markId)->first();
                        $post?->decrement("{$interactiveType}_count");
                        UserStat::where('user_id', $post?->user_id)->decrement("post_{$interactiveType}_count");
                    break;

                    // comment
                    case 'comment':
                        UserStat::where('user_id', $userId)->decrement("{$interactiveType}_group_count");

                        InteractiveUtility::markStatsComment($markId, $interactiveType, 'decrement');
                    break;
                }
            break;
        }
    }

    protected static function markStatsComment(int $commentId, string $interactiveType, string $actionType)
    {
        if (!in_array($actionType, ['increment', 'decrement'])) {
            return;
        }
        
        $comment = Comment::where('id', $commentId)->first();
        if (!$comment) {
            return;
        }

        $comment->$actionType("{$interactiveType}_count");
        UserStat::where('user_id', $comment->user_id)->$actionType("post_{$interactiveType}_count");
        Post::where('id', $comment->post_id)->$actionType("comment_{$interactiveType}_count");

        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)->$actionType("comment_{$interactiveType}_count");
            InteractiveUtility::markStatsComment($comment->parent_id, $interactiveType, $actionType);
        }
    }

    /**
     * It increments or decrements the stats of a post or comment
     *
     * @param string type post or comment
     * @param int id The id of the post or comment
     * @param string actionType increment or decrement
     */
    public static function publishStats(string $type, int $id, string $actionType)
    {
        switch ($actionType) {
            // increment
            case 'increment':
                switch ($type) {
                    // post
                    case 'post':
                        $post = Post::with('hashtags')->where('id', $id)->first();
                        UserStat::where('user_id', $post?->user_id)->increment('post_publish_count');
                        Group::where('id', $post?->group_id)->increment('post_count');

                        $linkIds = DomainLinkLinked::type(DomainLinkLinked::TYPE_POST)->where('linked_id', $post?->id)->pluck('link_id')->toArray();
                        DomainLink::whereIn('id', $linkIds)->increment('post_count');
                        $domainIds = DomainLink::whereIn('id', $linkIds)->pluck('domain_id')->toArray();
                        Domain::whereIn('id', $domainIds)->increment('post_count');

                        $hashtagIds = array_column($post->hashtags, 'id');
                        Hashtag::whereIn('id', $hashtagIds)->increment('post_count');
                    break;

                    // comment
                    case 'comment':
                        InteractiveUtility::publishStatsComment($id, 'increment');
                    break;
                }
            break;

            // decrement
            case 'decrement':
                switch ($type) {
                    // post
                    case 'post':
                        $post = Post::with('hashtags')->where('id', $id)->first();
                        UserStat::where('user_id', $post?->user_id)->decrement('post_publish_count');
                        Group::where('id', $post?->group_id)->decrement('post_count');

                        $linkIds = DomainLinkLinked::type(DomainLinkLinked::TYPE_POST)->where('linked_id', $post?->id)->pluck('link_id')->toArray();
                        DomainLink::whereIn('id', $linkIds)->decrement('post_count');
                        $domainIds = DomainLink::whereIn('id', $linkIds)->pluck('domain_id')->toArray();
                        Domain::whereIn('id', $domainIds)->decrement('post_count');

                        $hashtagIds = array_column($post->hashtags, 'id');
                        Hashtag::whereIn('id', $hashtagIds)->decrement('post_count');
                    break;

                    // comment
                    case 'comment':
                        InteractiveUtility::publishStatsComment($id, 'decrement');
                    break;
                }
            break;
        }
    }

    protected static function publishStatsComment(int $commentId, string $actionType)
    {
        if (!in_array($actionType, ['increment', 'decrement'])) {
            return;
        }

        $comment = Comment::with('hashtags')->where('id', $commentId)->first();
        if (!$comment) {
            return;
        }

        UserStat::where('user_id', $comment->user_id)->$actionType('comment_publish_count');
        Post::where('id', $comment->post_id)->$actionType("comment_count");
        Group::where('id', $comment->group_id)->$actionType('comment_count');

        $linkIds = DomainLinkLinked::type(DomainLinkLinked::TYPE_POST)->where('linked_id', $comment->id)->pluck('link_id')->toArray();
        DomainLink::whereIn('id', $linkIds)->$actionType('comment_count');

        $domainIds = DomainLink::whereIn('id', $linkIds)->pluck('domain_id')->toArray();
        Domain::whereIn('id', $domainIds)->$actionType('comment_count');

        $hashtagIds = array_column($comment->hashtags, 'id');
        Hashtag::whereIn('id', $hashtagIds)->$actionType('comment_count');

        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)->$actionType("comment_count");
            InteractiveUtility::publishStatsComment($comment->parent_id, $actionType);
        }
    }

    /**
     * It increments or decrements the digest count of a post or comment
     *
     * @param string type post or comment
     * @param int id the id of the post or comment
     * @param string actionType increment or decrement
     */
    public static function digestStats(string $type, int $id, string $actionType)
    {
        switch ($actionType) {
            // increment
            case 'increment':
                switch ($type) {
                    // post
                    case 'post':
                        $post = Post::with('hashtags')->where('id', $id)->first();
                        UserStat::where('user_id', $post?->user_id)->increment('post_digest_count');
                        Group::where('id', $post?->group_id)->increment('post_digest_count');

                        $hashtagIds = array_column($post->hashtags, 'id');
                        Hashtag::whereIn('id', $hashtagIds)->increment('post_digest_count');
                    break;

                    // comment
                    case 'comment':
                        InteractiveUtility::digestStatsComment($id, 'increment');
                    break;
                }
            break;

            // decrement
            case 'decrement':
                switch ($type) {
                    // post
                    case 'post':
                        $post = Post::with('hashtags')->where('id', $id)->first();
                        UserStat::where('user_id', $post?->user_id)->decrement('post_digest_count');
                        Group::where('id', $post?->group_id)->decrement('post_digest_count');

                        $hashtagIds = array_column($post->hashtags, 'id');
                        Hashtag::whereIn('id', $hashtagIds)->decrement('post_digest_count');
                    break;

                    // comment
                    case 'comment':
                        InteractiveUtility::digestStatsComment($id, 'decrement');
                    break;
                }
            break;
        }
    }

    protected static function digestStatsComment(int $commentId, string $actionType)
    {
        if (!in_array($actionType, ['increment', 'decrement'])) {
            return;
        }

        $comment = Comment::with('hashtags')->where('id', $commentId)->first();
        if (!$comment) {
            return;
        }

        UserStat::where('user_id', $comment->user_id)->$actionType('comment_digest_count');
        Post::where('id', $comment->post_id)->$actionType("comment_digest_count");
        Group::where('id', $comment->group_id)->$actionType('comment_digest_count');

        $hashtagIds = array_column($comment->hashtags, 'id');
        Hashtag::whereIn('id', $hashtagIds)->$actionType('comment_digest_count');

        Comment::where('id', $comment->parent_id)->$actionType("comment_digest_count");

        if ($comment->parent_id) {
            Comment::where('id', $comment->parent_id)->$actionType("comment_digest_count");
            InteractiveUtility::publishStatsComment($comment->parent_id, $actionType);
        }
    }
}
