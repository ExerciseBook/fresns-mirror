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
    // get the users who marked it
    public function getUsersWhoMarkIt(string $getType, string $markType, int $markId, string $timeOrder, string $langTag, string $timezone, ?string $authUserId = null)
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

        $service = new UserService();

        $paginateData = [];
        foreach ($interactiveData as $interactive) {
            $paginateData[] = $service->userList($interactive->creator, $langTag, $timezone, $authUserId);
        }

        return [
            'paginateData' => $paginateData,
            'interactiveData' => $interactiveData,
        ];
    }

    // get a list of the content it marks
    public function getItMarkList(string $getType, string $markType, int $userId, string $timeOrder, string $langTag, string $timezone, ?string $authUserId = null)
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
            ->type($markType)
            ->orderBy('created_at', $timeOrder)
            ->paginate(\request()->get('pageSize', 15));

        $markTypeName = match ($markType) {
            1 => 'user',
            2 => 'group',
            3 => 'hashtag',
            4 => 'post',
            5 => 'comment',
        };

        $paginateData = [];

        switch ($markTypeName) {
            // user
            case 'user':
                $service = new UserService();
                foreach ($markData as $mark) {
                    $paginateData[] = $service->userList($mark->user, $langTag, $timezone, $authUserId);
                }
            break;

            // group
            case 'group':
                $service = new GroupService();
                foreach ($markData as $mark) {
                    $paginateData[] = $service->groupList($mark->group, $langTag, $timezone, $authUserId);
                }
            break;

            // hashtag
            case 'hashtag':
                $service = new HashtagService();
                foreach ($markData as $mark) {
                    $paginateData[] = $service->hashtagList($mark->hashtag, $langTag, $timezone, $authUserId);
                }
            break;

            // post
            case 'post':
                $service = new PostService();
                foreach ($markData as $mark) {
                    $paginateData[] = $service->postDetail($mark->post, 'list', $langTag, $timezone, $authUserId);
                }
            break;

            // comment
            case 'comment':
                $service = new CommentService();
                foreach ($markData as $mark) {
                    $paginateData[] = $service->commentDetail($mark->comment, 'list', $langTag, $timezone, $authUserId);
                }
            break;
        }

        return [
            'paginateData' => $paginateData,
            'markData' => $markData,
        ];
    }
}
