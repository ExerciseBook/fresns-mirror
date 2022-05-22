<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\InteractiveHelper;
use App\Models\User;
use App\Models\Seo;
use App\Utilities\ExtendUtility;
use App\Exceptions\ApiException;
use Illuminate\Http\Request;

class UserController extends Controller
{
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

        $seoData = Seo::where('linked_type', 1)->where('linked_id', $viewUser->id)->where('lang_tag', $headers['langTag'])->first();
        $common['title'] = $seoData->title ?? null;
        $common['keywords'] = $seoData->keywords ?? null;
        $common['description'] = $seoData->description ?? null;
        $common['manages'] = [];
        $common['features'] = [];
        $common['profiles'] = [];
        if ($headers['uid'] == $viewUser->uid) {
            $common['manages'] = ExtendUtility::getPluginExtends(5, null, 3, $viewUser->id, $headers['langTag']);
            $common['features'] = ExtendUtility::getPluginExtends(7, null, null, $viewUser->id, $headers['langTag']);
            $common['profiles'] = ExtendUtility::getPluginExtends(8, null, null, $viewUser->id, $headers['langTag']);
        }
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
}
