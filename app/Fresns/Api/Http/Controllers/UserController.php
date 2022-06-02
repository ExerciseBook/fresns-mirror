<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\InteractiveHelper;
use App\Models\CommentLog;
use App\Models\PostLog;
use App\Helpers\PrimaryHelper;
use App\Models\Dialog;
use App\Models\DialogMessage;
use App\Models\Notify;
use App\Models\User;
use App\Models\Seo;
use App\Utilities\ExtendUtility;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // detail
    public function detail(string $uidOrUsername)
    {
        $headers = AppHelper::getApiHeaders();

        if (is_numeric($uidOrUsername)) {
            $viewUser = User::whereUid($uidOrUsername)->first();
        } else {
            $viewUser = User::whereUsername($uidOrUsername)->first();
        }

        if (empty($viewUser)) {
            throw new ApiException(31602);
        }

        $userId = null;
        if (! empty($headers['uid'])) {
            $userId = PrimaryHelper::fresnsUserIdByUid($headers['uid']);
        }

        $seoData = Seo::where('linked_type', 1)->where('linked_id', $viewUser->id)->where('lang_tag', $headers['langTag'])->first();
        $common['title'] = $seoData->title ?? null;
        $common['keywords'] = $seoData->keywords ?? null;
        $common['description'] = $seoData->description ?? null;
        $common['manages'] = ExtendUtility::getPluginExtends(5, null, 3, $userId, $headers['langTag']);
        $data['commons'] = $common;

        $userProfile = $viewUser->getUserProfile($headers['langTag'], $headers['timezone']);
        $userMainRole = $viewUser->getUserMainRole($headers['langTag'], $headers['timezone']);
        $userInteractive = InteractiveHelper::fresnsUserInteractive($headers['langTag']);

        $item['stats'] = $viewUser->getUserStats($headers['langTag']);
        $item['roles'] = $viewUser->getUserRoles($headers['langTag'], $headers['timezone']);
        $item['archives'] = $viewUser->getUserArchives($headers['langTag']);
        $item['icons'] = ExtendUtility::getIcons(1, $viewUser->id, $headers['langTag']);
        $item['tips'] = ExtendUtility::getTips(1, $viewUser->id, $headers['langTag']);
        $item['extends'] = ExtendUtility::getExtends(1, $viewUser->id, $headers['langTag']);

        $data['detail'] = array_merge($userProfile, $userMainRole, $item, $userInteractive);

        return $this->success($data);
    }

    // overview
    public function overview()
    {
        $headers = AppHelper::getApiHeaders();
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
