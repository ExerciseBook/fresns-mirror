<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Helpers\AppHelper;
use App\Helpers\InteractiveHelper;
use App\Models\Group;
use App\Models\User;
use App\Models\Seo;
use App\Utilities\ExtendUtility;
use App\Utilities\PermissionUtility;
use Illuminate\Http\Request;
use App\Exceptions\ApiException;

class GroupController extends Controller
{
    public function list(Request $request)
    {
        $headers = AppHelper::getApiHeaders();
        $user = ! empty($headers['uid']) ? User::whereUid($headers['uid'])->first() : null;

        $groups = Group::paginate($request->get('pageSize', 1));

        $groupList = [];
        foreach ($groups as $group) {
            $groupInfo = $group->getGroupInfo($headers['langTag']);

            $item['icons'] = ExtendUtility::getIcons(2, $group->id, $headers['langTag']);
            $item['tips'] = ExtendUtility::getTips(2, $group->id, $headers['langTag']);
            $item['extends'] = ExtendUtility::getExtends(2, $group->id, $headers['langTag']);

            $item['creator'] = null;
            if (! empty($creator)) {
                $userProfile = $creator->getUserProfile($headers['langTag'], $headers['timezone']);
                $userMainRole = $creator->getUserMainRole($headers['langTag'], $headers['timezone']);
                $item['creator'] = array_merge($userProfile, $userMainRole);
            }

            $groupList[] = array_merge($groupInfo, $item);
        }

        return $this->fresnsPaginate($groupList, $groups->total(), $groups->perPage());
    }

    public function detail(string $gid)
    {
        $headers = AppHelper::getApiHeaders();
        $user = ! empty($headers['uid']) ? User::whereUid($headers['uid'])->first() : null;

        $group = Group::whereGid($gid)->first();
        if (empty($group)) {
            throw new ApiException(37100);
        }

        $parentGroup = $group->category;
        $creator = $group->creator;

        $seoData = Seo::where('linked_type', 2)->where('linked_id', $group->id)->where('lang_tag', $headers['langTag'])->first();
        $common['title'] = $seoData->title ?? null;
        $common['keywords'] = $seoData->keywords ?? null;
        $common['description'] = $seoData->description ?? null;
        $common['extensions'] = ExtendUtility::getPluginExtends(6, $group->id, null, $user?->id, $headers['langTag']);
        $data['commons'] = $common;

        $groupInfo = $group->getGroupInfo($headers['langTag']);

        $item['publishRule'] = PermissionUtility::checkUserGroupPublishPerm($user?->id, $group->id);

        $item['icons'] = ExtendUtility::getIcons(2, $group->id, $headers['langTag']);
        $item['tips'] = ExtendUtility::getTips(2, $group->id, $headers['langTag']);
        $item['extends'] = ExtendUtility::getExtends(2, $group->id, $headers['langTag']);

        $item['parentInfo'] = null;
        if ($group->type == 2) {
            $item['parentInfo'] = $parentGroup->getParentGroupInfo($headers['langTag']);
        }

        $item['creator'] = null;
        if (! empty($creator)) {
            $userProfile = $creator->getUserProfile($headers['langTag'], $headers['timezone']);
            $userMainRole = $creator->getUserMainRole($headers['langTag'], $headers['timezone']);
            $item['creator'] = array_merge($userProfile, $userMainRole);
        }

        $item['admins'] = $group->getGroupAdmins($headers['langTag'], $headers['timezone']);

        $groupInteractive = InteractiveHelper::fresnsGroupInteractive($headers['langTag']);

        $data['detail'] = array_merge($groupInfo, $item, $groupInteractive);

        return $this->success($data);
    }
}
