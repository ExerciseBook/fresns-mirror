<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Fresns\Api\Http\DTO\UserListDTO;
use App\Fresns\Api\Http\DTO\MarkListDTO;
use App\Fresns\Api\Http\DTO\InteractiveDTO;
use App\Fresns\Api\Services\HeaderService;
use App\Models\CommentLog;
use App\Models\PostLog;
use App\Helpers\PrimaryHelper;
use App\Models\Dialog;
use App\Models\DialogMessage;
use App\Models\Notify;
use App\Models\User;
use App\Models\Seo;
use App\Models\PluginUsage;
use App\Utilities\ExtendUtility;
use App\Exceptions\ApiException;
use App\Fresns\Api\Services\UserService;
use App\Fresns\Api\Services\InteractiveService;
use Illuminate\Http\Request;
use App\Models\UserStat;
use App\Models\UserFollow;
use App\Models\UserBlock;

class UserController extends Controller
{
    // list
    public function list(Request $request)
    {
        $dtoRequest = new UserListDTO($request->all());
        $headers = HeaderService::getHeaders();

        $authUserId = null;
        if (! empty($headers['uid'])) {
            $authUserId = PrimaryHelper::fresnsUserIdByUid($headers['uid']);
        }

        $userQuery = UserStat::with('user');

        $userQuery->when($dtoRequest->verified, function ($query, $value) {
            $query->whereRelation('user', 'verified', $value);
        });

        $userQuery->when($dtoRequest->gender, function ($query, $value) {
            $query->whereRelation('user', 'gender', $value);
        });

        if ($dtoRequest->createTimeGt) {
            $userQuery->where('created_at', '>=', $dtoRequest->createTimeGt);
        }

        if ($dtoRequest->createTimeLt) {
            $userQuery->where('created_at', '<=', $dtoRequest->createTimeLt);
        }

        if ($dtoRequest->likeCountGt) {
            $userQuery->where('like_me_count', '>=', $dtoRequest->likeCountGt);
        }

        if ($dtoRequest->likeCountLt) {
            $userQuery->where('like_me_count', '<=', $dtoRequest->likeCountLt);
        }

        if ($dtoRequest->dislikeCountGt) {
            $userQuery->where('dislike_me_count', '>=', $dtoRequest->dislikeCountGt);
        }

        if ($dtoRequest->dislikeCountLt) {
            $userQuery->where('dislike_me_count', '<=', $dtoRequest->dislikeCountLt);
        }

        if ($dtoRequest->followCountGt) {
            $userQuery->where('follow_me_count', '>=', $dtoRequest->followCountGt);
        }

        if ($dtoRequest->followCountLt) {
            $userQuery->where('follow_me_count', '<=', $dtoRequest->followCountLt);
        }

        if ($dtoRequest->blockCountGt) {
            $userQuery->where('block_me_count', '>=', $dtoRequest->blockCountGt);
        }

        if ($dtoRequest->blockCountLt) {
            $userQuery->where('block_me_count', '<=', $dtoRequest->blockCountLt);
        }

        if ($dtoRequest->postCountGt) {
            $userQuery->where('post_publish_count', '>=', $dtoRequest->postCountGt);
        }

        if ($dtoRequest->postCountLt) {
            $userQuery->where('post_publish_count', '<=', $dtoRequest->postCountLt);
        }

        if ($dtoRequest->commentCountGt) {
            $userQuery->where('comment_publish_count', '>=', $dtoRequest->commentCountGt);
        }

        if ($dtoRequest->commentCountLt) {
            $userQuery->where('comment_publish_count', '<=', $dtoRequest->commentCountLt);
        }

        if ($dtoRequest->extcredits1CountGt) {
            $userQuery->where('extcredits1', '>=', $dtoRequest->extcredits1CountGt);
        }

        if ($dtoRequest->extcredits1CountLt) {
            $userQuery->where('extcredits1', '<=', $dtoRequest->extcredits1CountLt);
        }

        if ($dtoRequest->extcredits2CountGt) {
            $userQuery->where('extcredits2', '>=', $dtoRequest->extcredits2CountGt);
        }

        if ($dtoRequest->extcredits2CountLt) {
            $userQuery->where('extcredits2', '<=', $dtoRequest->extcredits2CountLt);
        }

        if ($dtoRequest->extcredits3CountGt) {
            $userQuery->where('extcredits3', '>=', $dtoRequest->extcredits3CountGt);
        }

        if ($dtoRequest->extcredits3CountLt) {
            $userQuery->where('extcredits3', '<=', $dtoRequest->extcredits3CountLt);
        }

        if ($dtoRequest->extcredits4CountGt) {
            $userQuery->where('extcredits4', '>=', $dtoRequest->extcredits4CountGt);
        }

        if ($dtoRequest->extcredits4CountLt) {
            $userQuery->where('extcredits4', '<=', $dtoRequest->extcredits4CountLt);
        }

        if ($dtoRequest->extcredits5CountGt) {
            $userQuery->where('extcredits5', '>=', $dtoRequest->extcredits5CountGt);
        }

        if ($dtoRequest->extcredits5CountLt) {
            $userQuery->where('extcredits5', '<=', $dtoRequest->extcredits5CountLt);
        }

        $ratingType = match ($dtoRequest->ratingType) {
            default => 'created_at',
            'like' => 'like_me_count',
            'dislike' => 'dislike_me_count',
            'follow' => 'follow_me_count',
            'block' => 'block_me_count',
            'post' => 'post_publish_count',
            'comment' => 'comment_publish_count',
            'extcredits1' => 'extcredits1',
            'extcredits2' => 'extcredits2',
            'extcredits3' => 'extcredits3',
            'extcredits4' => 'extcredits4',
            'extcredits5' => 'extcredits5',
            'createTime' => 'created_at',
        };

        $ratingOrder = match ($dtoRequest->ratingOrder) {
            default => 'desc',
            'asc' => 'asc',
            'desc' => 'desc',
        };

        $userQuery->orderBy($ratingType, $ratingOrder);

        $userData = $userQuery->paginate($request->get('pageSize', 15));

        $userList = [];
        $service = new UserService();
        foreach ($userData as $user) {
            $userList[] = $service->userList($user->user, $headers['langTag'], $headers['timezone'], $authUserId);
        }

        return $this->fresnsPaginate($userList, $userData->total(), $userData->perPage());
    }

    // detail
    public function detail(string $uidOrUsername)
    {
        if (is_numeric($uidOrUsername)) {
            $viewUser = User::whereUid($uidOrUsername)->first();
        } else {
            $viewUser = User::whereUsername($uidOrUsername)->first();
        }

        if (empty($viewUser)) {
            throw new ApiException(31602);
        }

        $headers = HeaderService::getHeaders();

        $authUserId = null;
        if (! empty($headers['uid'])) {
            $authUserId = PrimaryHelper::fresnsUserIdByUid($headers['uid']);
        }

        $seoData = Seo::where('linked_type', Seo::TYPE_USER)->where('linked_id', $viewUser->id)->where('lang_tag', $headers['langTag'])->first();

        $common['title'] = $seoData->title ?? null;
        $common['keywords'] = $seoData->keywords ?? null;
        $common['description'] = $seoData->description ?? null;
        $common['manages'] = ExtendUtility::getPluginExtends(PluginUsage::TYPE_MANAGE, null, PluginUsage::SCENE_USER, $authUserId, $headers['langTag']);
        $data['commons'] = $common;

        $service = new UserService();
        $data['detail'] = $service->userDetail($viewUser, $headers['langTag'], $headers['timezone'], $authUserId);

        return $this->success($data);
    }

    // interactive
    public function interactive(string $uidOrUsername, string $type, Request $request)
    {
        if (is_numeric($uidOrUsername)) {
            $viewUser = User::whereUid($uidOrUsername)->first();
        } else {
            $viewUser = User::whereUsername($uidOrUsername)->first();
        }

        if (empty($viewUser)) {
            throw new ApiException(31602);
        }

        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new InteractiveDTO($requestData);

        $timeOrder = $dtoRequest->timeOrder ?: 'desc';

        $headers = HeaderService::getHeaders();
        $authUserId = null;
        if (! empty($headers['uid'])) {
            $authUserId = PrimaryHelper::fresnsUserIdByUid($headers['uid']);
        }

        $service = new InteractiveService();
        $data = $service->getUsersWhoMarkIt($dtoRequest->type, InteractiveService::TYPE_USER, $viewUser->id, $timeOrder, $headers['langTag'], $headers['timezone'], $authUserId);

        return $this->fresnsPaginate($data['paginateData'], $data['interactiveData']->total(), $data['interactiveData']->perPage());
    }

    // like
    public function like(string $uidOrUsername, string $type, Request $request)
    {
        if (is_numeric($uidOrUsername)) {
            $viewUser = User::whereUid($uidOrUsername)->first();
        } else {
            $viewUser = User::whereUsername($uidOrUsername)->first();
        }

        if (empty($viewUser)) {
            throw new ApiException(31602);
        }

        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new MarkListDTO($requestData);

        $headers = HeaderService::getHeaders();
        $authUserId = null;
        if (! empty($headers['uid'])) {
            $authUserId = PrimaryHelper::fresnsUserIdByUid($headers['uid']);
        }

        $timeOrder = $dtoRequest->timeOrder ?: 'desc';

        $service = new InteractiveService();

        switch ($dtoRequest->type) {
            // user
            case 'user':
                $data = $service->getMarkUserList($dtoRequest->type, $viewUser->id, $timeOrder, $authUserId);
            break;

            // group
            case 'group':
            break;

            // hashtag
            case 'hashtag':
                // todo: markId 变量不存在,需要处理
                $interactiveQuery = UserFollow::where('follow_id', $markId);
            break;

            // post
            case 'post':
                $interactiveQuery = UserBlock::where('block_id', $markId);
            break;

            // comment
            case 'comment':
                $interactiveQuery = UserBlock::where('block_id', $markId);
            break;
        }

        return $this->fresnsPaginate($data['paginateData'], $data['markData']->total(), $data['markData']->perPage());
    }

    // panel
    public function panel()
    {
        $headers = HeaderService::getHeaders();
        $userId = PrimaryHelper::fresnsUserIdByUid($headers['uid']);

        if (empty($userId)) {
            throw new ApiException(31602);
        }

        $data['features'] = ExtendUtility::getPluginExtends(7, null, null, $userId, $headers['langTag']);
        $data['profiles'] = ExtendUtility::getPluginExtends(8, null, null, $userId, $headers['langTag']);

        $dialogACount = Dialog::where('a_user_id', $userId)->where('a_is_read', 0)->where('a_is_display', 1)->count();
        $dialogBCount = Dialog::where('b_user_id', $userId)->where('b_is_read', 0)->where('b_is_display', 1)->count();
        $dialogMessageCount = DialogMessage::where('recv_user_id', $userId)->where('recv_read_at', null)->where('recv_deleted_at', null)->isEnable()->count();
        $dialogUnread['dialog'] = $dialogACount + $dialogBCount;
        $dialogUnread['message'] = $dialogMessageCount;
        $data['dialogUnread'] = $dialogUnread;

        $notify = Notify::where('user_id', $userId)->where('is_read', 0);
        $notifyUnread['system'] = $notify->where('action_type', 1)->count();
        $notifyUnread['follow'] = $notify->where('action_type', 2)->count();
        $notifyUnread['like'] = $notify->where('action_type', 3)->count();
        $notifyUnread['comment'] = $notify->where('action_type', 4)->count();
        $notifyUnread['mention'] = $notify->where('action_type', 5)->count();
        $notifyUnread['recommend'] = $notify->where('action_type', 6)->count();
        $data['notifyUnread'] = $notifyUnread;

        $draftCount['posts'] = PostLog::where('user_id', $userId)->whereIn('state', [1, 4])->count();
        $draftCount['comments'] = CommentLog::where('user_id', $userId)->whereIn('state', [1, 4])->count();
        $data['draftCount'] = $draftCount;

        return $this->success($data);
    }
}
