<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Services;

use App\Models\Group;
use App\Models\UserBlock;
use App\Models\UserFollow;
use App\Models\UserLike;
use App\Models\UserStat;

class InteractiveService
{
    const TYPE_USER = 1;
    const TYPE_GROUP = 2;
    const TYPE_HASHTAG = 3;
    const TYPE_POST = 4;
    const TYPE_COMMENT = 5;

    const ACTION_CREATE = 'create';
    const ACTION_RESTORE = 'restore';
    const ACTION_DELETE = 'delete';

    protected static function userLike($authUserId, $markId, $likeType, callable $callback, $markType = UserLike::MARK_TYPE_LIKE)
    {
        $userLike = UserLike::withTrashed()
            ->where('user_id', $authUserId)
            ->markType($markType)
            ->type($likeType)
            ->where('like_id', $markId)
            ->first();

        $action = null;
        // 已经被删除的 或者 没有查到数据
        if ($userLike->trashed() || empty($userLike)) {
            if ($userLike->trashed()) {
                $userLike->restore();

                $action = InteractiveService::ACTION_RESTORE;
            } else {
                UserLike::updateOrCreate([
                    'user_id' => $authUserId,
                    'like_type' => $likeType,
                    'like_id' => $markId,
                ], [
                    'mark_type' => $markType,
                ]);

                $action = InteractiveService::ACTION_CREATE;
            }
        } else {
            $userLike->delete();

            $action = InteractiveService::ACTION_DELETE;
        }

        $callback($action, $authUserId, $markId, $markType, $likeType);
    }

    protected static function userFollow($authUserId, $markId, $likeType, callable $callback, $markType = 'follow')
    {
        $userFollow = UserFollow::withTrashed()
            ->where('user_id', $authUserId)
            ->type($likeType)
            ->where('follow_id', $markId)
            ->first();

        $action = null;
        if ($userFollow->trashed() || empty($userFollow)) {
            if ($userFollow->trashed()) {
                $userFollow->restore();

                $action = InteractiveService::ACTION_RESTORE;
            } else {
                UserFollow::updateOrCreate([
                    'user_id' => $authUserId,
                    'follow_type' => $likeType,
                    'follow_id' => $markId,
                ]);

                $action = InteractiveService::ACTION_CREATE;
            }
        } else {
            $userFollow->delete();

            $action = InteractiveService::ACTION_DELETE;
        }

        $callback($action, $authUserId, $markId, $markType, $likeType);
    }

    protected static function userBlock($authUserId, $markId, $likeType, callable $callback, $markType = 'block')
    {
        $userBlock = UserBlock::withTrashed()
            ->where('user_id', $authUserId)
            ->type($likeType)
            ->where('follow_id', $markId)
            ->first();

        $action = null;
        if ($userBlock->trashed() || empty($userBlock)) {
            if ($userBlock->trashed()) {
                $userBlock->restore();

                $action = InteractiveService::ACTION_RESTORE;
            } else {
                UserBlock::updateOrCreate([
                    'user_id' => $authUserId,
                    'block_type' => $likeType,
                    'block_id' => $markId,
                ]);

                $action = InteractiveService::ACTION_CREATE;
            }
        } else {
            $userBlock->delete();

            $action = InteractiveService::ACTION_DELETE;
        }

        $callback($action, $authUserId, $markId, $markType, $likeType);
    }

    protected static function markActionHandle(?string $action, $authUserId, $markId, $markType, $likeType)
    {
        if (is_null($action)) {
            return;
        }

        $markTypeField = match ($markType) {
            default => null,
            UserLike::MARK_TYPE_LIKE => 'like',
            UserLike::MARK_TYPE_DISLIKE => 'dislike',
            'follow' => 'follow',
            'block' => 'block',
        };

        if (is_null($markTypeField)) {
            return;
        }

        switch (true) {
                // create
            case $action === InteractiveService::ACTION_CREATE:
                // restore
            case $action === InteractiveService::ACTION_RESTORE:
                switch ($likeType) {
                    case UserLike::TYPE_USER:
                    case 'follow':
                    case 'block':
                        UserStat::where('user_id', $authUserId)->increment("{$markTypeField}_user_count");
                        UserStat::where('user_id', $markId)->increment("{$markTypeField}_me_count");
                        break;
                    case UserLike::TYPE_GROUP:
                        UserStat::where('user_id', $authUserId)->decrement("{$markTypeField}_group_count");
                        Group::where('id', $markId)->decrement("{$markTypeField}_count");
                        break;
                }
                break;
                // delete
            case $action === InteractiveService::ACTION_DELETE:
                switch ($likeType) {
                    case UserLike::TYPE_USER:
                        UserStat::where('user_id', $authUserId)->decrement("{$markTypeField}_user_count");
                        UserStat::where('user_id', $markId)->decrement("{$markTypeField}_me_count");
                        break;
                    case UserLike::TYPE_GROUP:
                        UserStat::where('user_id', $authUserId)->decrement("{$markTypeField}_group_count");
                        Group::where('id', $markId)->decrement("{$markTypeField}_count");
                        break;
                }
                break;
        }
    }

    public static function mark(int $markType, int $markId, int $authUserId, int $likeType): bool
    {
        switch ($markType) {
            case 'like':
            case 'dislike':
                $markType = match ($markType) {
                    default => UserLike::MARK_TYPE_LIKE,
                    'like' => UserLike::MARK_TYPE_LIKE,
                    'dislike' => UserLike::MARK_TYPE_DISLIKE,
                };

                InteractiveService::userLike(
                    $authUserId,
                    $markId,
                    $markType,
                    [InteractiveService::class, 'markActionHandle'],
                    $likeType
                );
                break;

                // follow
            case 'follow':
                InteractiveService::userFollow(
                    $authUserId,
                    $markId,
                    $likeType,
                    [InteractiveService::class, 'markActionHandle'],
                    'follow'
                );
                break;

                // block
            case 'block':
                InteractiveService::userBlock(
                    $authUserId,
                    $markId,
                    $likeType,
                    [InteractiveService::class, 'markActionHandle'],
                    'block'
                );
                break;
        }

        return true;
    }

    public static function checkUserLike(int $likeType, int $likeId, ?int $authUserId = null): bool
    {
        if (empty($authUserId)) {
            return false;
        }

        $checkLike = UserLike::where('user_id', $authUserId)
            ->markType(UserLike::MARK_TYPE_LIKE)
            ->type($likeType)
            ->where('like_id', $likeId)
            ->first();

        if ($checkLike) {
            return true;
        }

        return false;
    }

    public static function checkUserDislike(int $dislikeType, int $dislikeId, ?int $authUserId = null): bool
    {
        if (empty($authUserId)) {
            return false;
        }

        $checkDislike = UserLike::where('user_id', $authUserId)
            ->markType(UserLike::MARK_TYPE_DISLIKE)
            ->type($dislikeType)
            ->where('like_id', $dislikeId)
            ->first();

        if ($checkDislike) {
            return true;
        }

        return false;
    }

    public static function checkUserFollow(int $followType, int $followId, ?int $authUserId = null): bool
    {
        if (empty($authUserId)) {
            return false;
        }

        $checkFollow = UserFollow::where('user_id', $authUserId)
            ->type($followType)
            ->where('follow_id', $followId)
            ->first();

        return (bool) $checkFollow;
    }

    public static function checkUserBlock(int $blockType, int $blockId, ?int $authUserId = null): bool
    {
        if (empty($authUserId)) {
            return false;
        }

        $checkBlock = UserBlock::where('user_id', $authUserId)
            ->type($blockType)
            ->where('block_id', $blockId)
            ->first();

        return (bool) $checkBlock;
    }

    public static function checkUserFollowMe(int $userId, ?int $authUserId = null): bool
    {
        if (empty($authUserId)) {
            return false;
        }

        if ($userId == $authUserId) {
            return false;
        }

        $checkFollowMe = UserFollow::where('user_id', $userId)
            ->type(UserFollow::TYPE_USER)
            ->where('follow_id', $authUserId)
            ->first();

        return (bool) $checkFollowMe;
    }

    public static function checkInteractiveStatus(int $markType, int $markId, ?int $authUserId = null): array
    {
        if (empty($authUserId)) {
            $status['likeStatus'] = false;
            $status['dislikeStatus'] = false;
            $status['followStatus'] = false;
            $status['blockStatus'] = false;

            return $status;
        }

        $status['likeStatus'] = self::checkUserLike($markType, $markId, $authUserId);
        $status['dislikeStatus'] = self::checkUserDislike($markType, $markId, $authUserId);
        $status['followStatus'] = self::checkUserFollow($markType, $markId, $authUserId);
        $status['blockStatus'] = self::checkUserBlock($markType, $markId, $authUserId);

        return $status;
    }

    public function getMarkListOfUsers(string $getType, string $markType, int $markId, string $timeOrder, ?string $authUserId = null)
    {
        switch ($getType) {
                // like
            case 'like':
                $interactiveQuery = UserLike::markType(UserLike::MARK_TYPE_LIKE)->where('like_id', $markId);
                break;

                // dislike
            case 'dislike':
                $interactiveQuery = UserLike::markType(UserLike::MARK_TYPE_DISLIKE)->where('like_id', $markId);
                break;

                // follow
            case 'follow':
                $interactiveQuery = UserFollow::where('follow_id', $markId);
                break;

                // block
            case 'block':
                $interactiveQuery = UserBlock::where('block_id', $markId);
                break;
        }

        $interactiveData = $interactiveQuery->with('creator')
            ->type($markType)
            ->orderBy('created_at', $timeOrder)
            ->paginate(\request()->get('pageSize', 15));

        $headers = HeaderService::getHeaders();

        $service = new UserService();

        $paginateData = [];
        foreach ($interactiveData as $interactive) {
            $paginateData[] = $service->userDetail($interactive->creator, 'list', $headers['langTag'], $headers['timezone'], $authUserId);
        }

        return [
            'paginateData' => $paginateData,
            'interactiveData' => $interactiveData,
        ];
    }

    public function getMarkUserList(string $getType, int $userId, string $timeOrder, ?string $authUserId = null)
    {
        switch ($getType) {
                // like
            case 'like':
                $markQuery = UserLike::markType(UserLike::MARK_TYPE_LIKE);
                break;

                // dislike
            case 'dislike':
                $markQuery = UserLike::markType(UserLike::MARK_TYPE_DISLIKE);
                break;

                // follow
            case 'follow':
                $markQuery = UserFollow::query();
                break;

                // block
            case 'block':
                $markQuery = UserBlock::query();
                break;
        }

        $markData = $markQuery->with('user')
            ->where('user_id', $userId)
            ->type(InteractiveService::TYPE_USER)
            ->orderBy('created_at', $timeOrder)
            ->paginate(\request()->get('pageSize', 15));

        $headers = HeaderService::getHeaders();

        $service = new UserService();

        $paginateData = [];
        foreach ($markData as $mark) {
            $paginateData[] = $service->userList($mark->user, $headers['langTag'], $headers['timezone'], $authUserId);
        }

        return [
            'paginateData' => $paginateData,
            'markData' => $markData,
        ];
    }
}
