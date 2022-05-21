<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\ConfigHelper;
use App\Models\GroupAdmin;
use App\Models\User;
use App\Models\UserRole;

class PermissionUtility
{
    // Check if the user belongs to the account
    public static function checkUserAffiliation(int $userId, int $accountId)
    {
        $userAccountId = User::where('id', $userId)->value('account_id');

        return $userAccountId == $accountId ? 'true' : 'false';
    }

    // Check user status
    public static function checkUserStatus(int $userId)
    {
        $userStatus = User::where('id', $userId)->value('is_enable');

        if (empty($userStatus)) {
            return 'false';
        }

        return $userStatus == 0 ? 'true' : 'false';
    }

    // Check user role permissions
    public static function checkUserRolePerm(int $userId, array $permRoleIds)
    {
        $userRoles = UserRole::where('user_id', $userId)->pluck('role_id')->toArray();

        return array_intersect($permRoleIds, $userRoles) ? 'true' : 'false';
    }

    // Check if the user has permission to publish
    public static function checkUserPublishPermForPost(int $userId, ?string $langTag = null)
    {
        $publishConfig = ConfigHelper::fresnsConfigByItemTag('postEditor', $langTag);
        $user = User::find($userId);
    }

    public static function checkUserPublishPermForComment(int $userId, ?string $langTag = null)
    {
        $publishConfig = ConfigHelper::fresnsConfigByItemTag('commentEditor', $langTag);
        $user = User::find($userId);
    }

    // Check if the user is a group administrator
    public static function checkUserGroupAdmin(int $userId, int $groupId)
    {
        $groupAdminArr = GroupAdmin::where('group_id', $groupId)->pluck('user_id')->toArray();

        return in_array($userId, $groupAdminArr) ? 'true' : 'false';
    }

    // Check if the user has group publishing permissions
    public static function checkUserGroupPublishPerm(?int $userId = null, int $groupId)
    {
        $perm['allowPost'] = true;
        $perm['reviewPost'] = true;
        $perm['allowComment'] = true;
        $perm['reviewComment'] = true;
        $perms = $perm;

        return $perms;
    }
}
