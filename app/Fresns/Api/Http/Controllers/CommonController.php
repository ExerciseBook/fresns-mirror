<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Fresns\Api\Http\DTO\CommonCallbacksDTO;
use App\Fresns\Api\Http\DTO\CommonInputTipsDTO;
use App\Fresns\Api\Services\AccountService;
use App\Helpers\AppHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\FileHelper;
use App\Models\Extend;
use App\Models\Hashtag;
use App\Models\Language;
use App\Models\Plugin;
use App\Models\Post;
use App\Models\User;
use App\Exceptions\ApiException;
use App\Models\PluginCallback;
use App\Utilities\EditorUtility;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    // inputTips
    public function inputTips(Request $request)
    {
        $dtoRequest = new CommonInputTipsDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        switch ($dtoRequest->type) {
            // user
            case 1:
                $userQuery = User::where('username', 'like', "%$dtoRequest->key%")
                    ->orWhere('nickname', 'like', "%$dtoRequest->key%")
                    ->limit(10)
                    ->get();

                if (ConfigHelper::fresnsConfigFileValueTypeByItemKey('default_avatar') == 'URL') {
                    $defaultAvatar = ConfigHelper::fresnsConfigByItemKey('default_avatar');
                } else {
                    $fresnsResp = \FresnsCmdWord::plugin('Fresns')->getFileInfo([
                        'fileId' => ConfigHelper::fresnsConfigByItemKey('default_avatar'),
                    ]);
                    $defaultAvatar = $fresnsResp->getData('imageAvatarUrl');
                }

                $data = null;
                foreach ($userQuery as $user) {
                    $avatar = FileHelper::fresnsFileImageUrlByColumn($user->avatar_file_id, $user->avatar_file_url, 'image_thumb_avatar');

                    $item['fsid'] = $user->uid;
                    $item['name'] = $user->username;
                    $item['nickname'] = $user->nickname;
                    $item['image'] = $avatar = $avatar ?: $defaultAvatar;
                    $item['followStatus'] = false;
                    $data[] = $item;
                }
            break;

            // group
            case 2:
                $tipQuery = Language::where('table_name', 'groups')
                    ->where('table_column', 'name')
                    ->where('lang_content', 'like', "%$dtoRequest->key%")
                    ->value('table_id')
                    ->limit(10)
                    ->get()
                    ->toArray();

                $groupIds = array_unique($tipQuery);

                $groupQuery = Language::whereIn('id', $groupIds)->get();

                $data = null;
                foreach ($groupQuery as $group) {
                    $item['fsid'] = $group->gid;
                    $item['name'] = LanguageHelper::fresnsLanguageByTableId('groups', 'name', $group->id, $headers['langTag']);
                    $item['nickname'] = null;
                    $item['image'] = FileHelper::fresnsFileImageUrlByColumn($group->cover_file_id, $group->cover_file_url);
                    $item['followStatus'] = false;
                    $data[] = $item;
                }
            break;

            // hashtag
            case 3:
                $hashtagQuery = Hashtag::where('name', 'like', "%$dtoRequest->key%")->limit(10)->get();

                $data = null;
                foreach ($hashtagQuery as $hashtag) {
                    $item['fsid'] = $hashtag->slug;
                    $item['name'] = $hashtag->name;
                    $item['nickname'] = null;
                    $item['image'] = FileHelper::fresnsFileImageUrlByColumn($hashtag->cover_file_id, $hashtag->cover_file_url);
                    $item['followStatus'] = false;
                    $data[] = $item;
                }
            break;

            // post
            case 4:
                $postQuery = Post::where('title', 'like', "%$dtoRequest->key%")->limit(10)->get();

                $data = null;
                foreach ($postQuery as $post) {
                    $item['fsid'] = $post->pid;
                    $item['name'] = $post->title;
                    $item['nickname'] = null;
                    $item['image'] = null;
                    $item['followStatus'] = false;
                    $data[] = $item;
                }
            break;

            // comment
            case 5:
                $data = null;
            break;

            // extend
            case 6:
                $tipQuery = Language::where('table_name', 'extends')
                    ->where('table_column', 'title')
                    ->where('lang_content', 'like', "%$dtoRequest->key%")
                    ->value('table_id')
                    ->limit(10)
                    ->get()
                    ->toArray();

                $extendIds = array_unique($tipQuery);

                $extendQuery = Extend::whereIn('id', $extendIds)->get();

                $data = null;
                foreach ($extendQuery as $extend) {
                    $item['fsid'] = $extend->eid;
                    $item['name'] = LanguageHelper::fresnsLanguageByTableId('extends', 'title', $extend->id, $headers['langTag']);
                    $item['nickname'] = null;
                    $item['image'] = FileHelper::fresnsFileImageUrlByColumn($extend->cover_file_id, $extend->cover_file_url);
                    $item['followStatus'] = false;
                    $data[] = $item;
                }
            break;
        }

        return $this->success($data);
    }

    // callbacks
    public function callbacks(Request $request)
    {
        $dtoRequest = new CommonCallbacksDTO($request->all());
        $headers = AppHelper::getApiHeaders();

        $plugin = Plugin::whereUnikey($dtoRequest->unikey)->first();
        if (empty($plugin)) {
            throw new ApiException(32304);
        }

        $callback = PluginCallback::whereUuid($dtoRequest->uuid)->first();

        if (empty($callback)) {
            throw new ApiException(32201);
        }

        if ($callback->is_use == 1) {
            throw new ApiException(32204);
        }

        $timeDifference = time() - strtotime($callback->created_at);
        if ($timeDifference > 600) {
            throw new ApiException(32203);
        }

        $data['types'] = explode(',', $callback->types);
        $data['dbContent'] = $callback->content;
        $data['apiContent'] = $callback->content;

        if (in_array(2, $data['types'])) {
            $service = new AccountService();
            $data['apiContent']['account'] = $service->accountDetail($callback->account_id);

            $fresnsResponse = \FresnsCmdWord::plugin()->createSessionToken([
                'platformId' => $headers['platformId'],
                'aid' => $data['apiContent']['account']['aid'],
            ]);

            if ($fresnsResponse->isSuccessResponse()) {
                $data['apiContent']['account']['token'] = $fresnsResponse->getData('token') ?? null;
                $data['apiContent']['account']['tokenExpiredTime'] = $fresnsResponse->getData('tokenExpiredTime') ?? null;
            }
        }

        if (in_array(4, $data['types'])) {
            $fids = array_column($callback->content['files'], 'fid');
            $data['apiContent']['files'] = FileHelper::fresnsAntiLinkFileInfoList($fids);
        }

        if (in_array(5, $data['types'])) {
            $data['apiContent']['extends'] = EditorUtility::extendHandle($callback->content['extends']);
        }

        if (in_array(6, $data['types'])) {
            $data['apiContent']['readAllowConfig'] = EditorUtility::readAllowHandle($callback->content['readAllowConfig']);
        }

        if (in_array(7, $data['types'])) {
            $data['apiContent']['userListConfig'] = EditorUtility::userListHandle($callback->content['userListConfig']);
        }

        if (in_array(8, $data['types'])) {
            $data['apiContent']['commentBtnConfig'] = EditorUtility::commentBtnHandle($callback->content['commentBtnConfig']);
        }

        $callback->is_use = 1;
        $callback->use_plugin_unikey = $dtoRequest->unikey;
        $callback->save();

        return $this->success($data);
    }
}
