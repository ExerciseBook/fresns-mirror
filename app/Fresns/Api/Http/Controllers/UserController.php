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
        $data = $service->getMarkListOfUsers($dtoRequest->type, InteractiveService::TYPE_USER, $viewUser->id, $timeOrder, $authUserId);

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
