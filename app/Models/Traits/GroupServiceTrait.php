<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Models\Traits;

use App\Helpers\FileHelper;
use App\Helpers\LanguageHelper;
use App\Helpers\PluginHelper;
use App\Models\GroupAdmin;
use App\Models\User;

trait GroupServiceTrait
{
    public function getGroupInfo(string $langTag = '')
    {
        $groupData = $this;

        $info['gid'] = $groupData->gid;
        $info['type'] = $groupData->type;
        $info['gname'] = LanguageHelper::fresnsLanguageByTableId('groups', 'name', $groupData->id, $langTag);
        $info['description'] = LanguageHelper::fresnsLanguageByTableId('groups', 'description', $groupData->id, $langTag);
        $info['cover'] = FileHelper::fresnsFileImageUrlByColumn($groupData->cover_file_id, $groupData->cover_file_url);
        $info['banner'] = FileHelper::fresnsFileImageUrlByColumn($groupData->banner_file_id, $groupData->banner_file_url);
        $info['recommend'] = $groupData->is_recommend;
        $info['mode'] = $groupData->type_mode;
        $info['find'] = $groupData->type_find;
        $info['followType'] = $groupData->type_follow;
        $info['followUrl'] = ! empty($groupData->plugin_unikey) ? PluginHelper::fresnsPluginUrlByUnikey($groupData->plugin_unikey) : null;
        $info['likeCount'] = $groupData->like_count;
        $info['followCount'] = $groupData->follow_count;
        $info['blockCount'] = $groupData->block_count;
        $info['postCount'] = $groupData->post_count;
        $info['digestCount'] = $groupData->digest_count;
        $info['permission'] = $groupData->permission;

        return $info;
    }

    public function getGroupAdmins(string $langTag = '', string $timezone = '')
    {
        $adminIds = $this->admins;

        $adminUsers = User::whereIn('id', $adminIds->pluck('user_id'))->first();

        $adminList = null;
        foreach ($adminIds as $groupAdmin) {
            $admin = $adminUsers->where('id', $groupAdmin->user_id)?->first();

            $userProfile = $admin->getUserProfile($timezone);
            $userMainRole = $admin->getUserMainRole($langTag, $timezone);

            $adminList[] = array_merge($userProfile, $userMainRole);
        }

        return $adminList;
    }

    public function getParentGroupInfo(string $langTag = '')
    {
        $parentGroup = $this;

        $info['gid'] = $parentGroup->gid;
        $info['gname'] = LanguageHelper::fresnsLanguageByTableId('groups', 'name', $parentGroup->id, $langTag);
        $info['description'] = LanguageHelper::fresnsLanguageByTableId('groups', 'description', $parentGroup->id, $langTag);
        $info['cover'] = FileHelper::fresnsFileImageUrlByColumn($parentGroup->cover_file_id, $parentGroup->cover_file_url);
        $info['banner'] = FileHelper::fresnsFileImageUrlByColumn($parentGroup->banner_file_id, $parentGroup->banner_file_url);

        return $info;
    }
}
