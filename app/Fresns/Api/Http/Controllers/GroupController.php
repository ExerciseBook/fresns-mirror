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
use App\Utilities\ConfigUtility;
use App\Utilities\ExpandUtility;
use App\Utilities\ValidationUtility;
use Illuminate\Http\Request;
use App\Exceptions\FresnsApiException;

class GroupController extends Controller
{
    public function list(Request $request)
    {

        throw new FresnsApiException(37100);


        $headers = AppHelper::getApiHeaders();
        $user = ! empty($headers['uid']) ? User::whereUid($headers['uid'])->first() : null;

        $groups = Group::paginate($request->get('pageSie', 1));

        $groupList = [];
        foreach ($groups as $group) {
            $groupInfo = $group->getGroupInfo($headers['langTag']);

            $item['icons'] = ExpandUtility::getIcons(2, $group->id, $headers['langTag']);
            $item['tips'] = ExpandUtility::getTips(2, $group->id, $headers['langTag']);
            $item['extends'] = ExpandUtility::getExtends(2, $group->id, $headers['langTag']);

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
            return $this->failure(
                37100,
                ConfigUtility::getCodeMessage(37100, 'Fresns', $headers['langTag'])
            );
        }

        $parentGroup = $group->category;
        $creator = $group->creator;

        $seoData = Seo::where('linked_type', 2)->where('linked_id', $group->id)->where('lang_tag', $headers['langTag'])->first();
        $common['title'] = $seoData->title ?? null;
        $common['keywords'] = $seoData->keywords ?? null;
        $common['description'] = $seoData->description ?? null;
        $common['extensions'] = ExpandUtility::getPluginExpands(6, $group->id, null, $user?->id, $headers['langTag']);
        $data['commons'] = $common;

        $groupInfo = $group->getGroupInfo($headers['langTag']);

        $item['publishRule'] = ValidationUtility::checkUserGroupPublishPerm($user?->id, $group->id);

        $item['icons'] = ExpandUtility::getIcons(2, $group->id, $headers['langTag']);
        $item['tips'] = ExpandUtility::getTips(2, $group->id, $headers['langTag']);
        $item['extends'] = ExpandUtility::getExtends(2, $group->id, $headers['langTag']);

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
