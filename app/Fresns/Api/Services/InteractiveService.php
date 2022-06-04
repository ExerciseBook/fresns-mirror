<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Services;

use App\Models\UserBlock;
use App\Models\UserFollow;
use App\Models\UserLike;

class InteractiveService
{
    const TYPE_USER = 1;
    const TYPE_GROUP = 2;
    const TYPE_HASHTAG = 3;
    const TYPE_POST = 4;
    const TYPE_COMMENT = 5;

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

        if ($checkFollow) {
            return true;
        }

        return false;
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

        if ($checkBlock) {
            return true;
        }

        return false;
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

        if ($checkFollowMe) {
            return true;
        }

        return false;
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
                $markQuery = UserLike::where('user_id', $userId)->markType(UserLike::MARK_TYPE_LIKE);
            break;

            // dislike
            case 'dislike':
                $markQuery = UserLike::where('user_id', $userId)->markType(UserLike::MARK_TYPE_DISLIKE);
            break;

            // follow
            case 'follow':
                $markQuery = UserFollow::where('user_id', $userId);
            break;

            // block
            case 'block':
                $markQuery = UserBlock::where('user_id', $userId);
            break;
        }

        $markData = $markQuery->with('user')
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
