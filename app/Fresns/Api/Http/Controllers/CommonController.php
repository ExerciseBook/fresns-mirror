<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Fresns\Api\Http\DTO\CommonInputTipsDTO;
use App\Helpers\AppHelper;
use App\Helpers\ConfigHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\FileHelper;
use App\Models\Extend;
use App\Models\Hashtag;
use App\Models\Language;
use App\Models\Post;
use App\Models\User;
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
}
