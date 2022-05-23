<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\ConfigHelper;
use App\Models\GroupAdmin;
use App\Models\PostAllow;
use App\Models\User;
use App\Models\UserRole;

class PermissionUtility
{
    // Check if the user belongs to the account
    public static function checkUserAffiliation(int $userId, int $accountId): bool
    {
        $userAccountId = User::where('id', $userId)->value('account_id');

        return $userAccountId == $accountId ? 'true' : 'false';
    }

    // Check user status of the private model
    public static function checkUserStatusOfPrivateModel(int $userId): bool
    {
        $userSet = User::where('id', $userId)->value('expired_at');

        $now = time();
        $expireTime = strtotime($userSet->expired_at);

        if ($expireTime && $expireTime < $now) {
            return true;
        }

        return false;
    }

    // Check user permissions
    public static function checkUserPerm(int $userId, array $permUserIds): bool
    {
        return in_array($userId, $permUserIds) ? 'true' : 'false';
    }

    // Check user role permissions
    public static function checkUserRolePerm(int $userId, array $permRoleIds): bool
    {
        $userRoles = UserRole::where('user_id', $userId)->pluck('role_id')->toArray();

        return array_intersect($userRoles, $permRoleIds) ? 'true' : 'false';
    }

    // Check post allow
    public static function checkPostAllow(int $userId, int $postId): bool
    {
        $allowUsers = PostAllow::where('post_id', $postId)->where('type', 1)->pluck('object_id')->toArray();
        $checkUser = PermissionUtility::checkUserPerm($userId, $allowUsers);
        if ($checkUser) {
            return true;
        } else {
            $allowRoles = PostAllow::where('post_id', $postId)->where('type', 2)->pluck('object_id')->toArray();
            return PermissionUtility::checkUserRolePerm($userId, $allowRoles);
        }
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
