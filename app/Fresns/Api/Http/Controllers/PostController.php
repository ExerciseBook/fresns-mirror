<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\InteractiveHelper;
use App\Models\File;
use App\Models\Post;
use App\Models\User;
use App\Models\Seo;
use App\Utilities\ExpandUtility;
use App\Utilities\LbsUtility;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function list(Request $request)
    {

    }

    public function detail(string $pid, Request $request)
    {
        $headers = AppHelper::getApiHeaders();
        $user = ! empty($headers['uid']) ? User::whereUid($headers['uid'])->first() : null;

        $post = Post::with('creator')->wherePid($pid)->first();
        if (empty($post)) {
            throw new ApiException(37300);
        }

        $seoData = Seo::where('linked_type', 4)->where('linked_id', $post->id)->where('lang_tag', $headers['langTag'])->first();
        $common['title'] = $seoData->title ?? null;
        $common['keywords'] = $seoData->keywords ?? null;
        $common['description'] = $seoData->description ?? null;
        $data['commons'] = $common;

        $postInfo = $post->getPostInfo($headers['langTag'], $headers['timezone'], 'detail');
        if (! empty($post->map_id)) {
            $postLat = $post->map_latitude;
            $postLon = $post->map_longitude;
            $userLat = $request->longitude;
            $userLon = $request->latitude;
            $postInfo['location']['distance'] = LbsUtility::getDistanceWithUnit($headers['langTag'], $postLat, $postLon, $userLat, $userLon);
        }

        $fileList = (new File)->getFileListInfo('posts', 'id', $post->id);
        $groupFileList = $fileList->groupBy('type');
        $files['images'] = $groupFileList->get(File::TYPE_IMAGE)?->all() ?? null;
        $files['videos'] = $groupFileList->get(File::TYPE_VIDEO)?->all() ?? null;
        $files['audios'] = $groupFileList->get(File::TYPE_AUDIO)?->all() ?? null;
        $files['documents'] = $groupFileList->get(File::TYPE_DOCUMENT)?->all() ?? null;
        $item['files'] = $files;
        $item['icons'] = ExpandUtility::getIcons(4, $post->id, $headers['langTag']);
        $item['tips'] = ExpandUtility::getTips(4, $post->id, $headers['langTag']);
        $item['extends'] = ExpandUtility::getExtends(4, $post->id, $headers['langTag']);

        $attachCount['images'] = $groupFileList->get(File::TYPE_IMAGE)?->count() ?? 0;
        $attachCount['videos'] = $groupFileList->get(File::TYPE_VIDEO)?->count() ?? 0;
        $attachCount['audios'] = $groupFileList->get(File::TYPE_AUDIO)?->count() ?? 0;
        $attachCount['documents'] = $groupFileList->get(File::TYPE_DOCUMENT)?->count() ?? 0;
        $attachCount['icons'] = collect($item['icons'])->count();
        $attachCount['tips'] = collect($item['tips'])->count();
        $attachCount['extends'] = collect($item['extends'])->count();
        $item['attachCount'] = $attachCount;

        $item['group'] = $post->group?->getGroupInfo($headers['langTag']);

        $item['creator'] = InteractiveHelper::fresnsUserAnonymousProfile();
        if (! $post->is_anonymous) {
            $creatorProfile = $post->creator->getUserProfile($headers['langTag'], $headers['timezone']);
            $creatorMainRole = $post->creator->getUserMainRole($headers['langTag'], $headers['timezone']);
            $item['creator'] = array_merge($creatorProfile, $creatorMainRole);
        }

        $item['manages'] = ExpandUtility::getPluginExpands(5, $post->group_id, 1, $user?->id, $headers['langTag']);

        $editStatus['isMe'] = false;
        $editStatus['canEdit'] = false;
        $editStatus['isPluginEditor'] = false;
        $editStatus['editorUrl'] = null;
        $editStatus['canDelete'] = false;

        $item['editStatus'] = $editStatus;

        $isMe = $post->user_id == $user?->id ? true : false;
        if ($isMe) {
            $item['editStatus'] = $post->getEditStatus($user->id);
        }

        $postInteractive = InteractiveHelper::fresnsPostInteractive($headers['langTag']);

        $data['detail'] = array_merge($postInfo, $item, $postInteractive);

        return $this->success($data);
    }
}
