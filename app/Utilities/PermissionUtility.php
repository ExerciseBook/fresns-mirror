<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Utilities;

use App\Helpers\ConfigHelper;
use App\Models\Group;
use App\Models\GroupAdmin;
use App\Models\PostAllow;
use App\Models\Role;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\UserRole;

class PermissionUtility
{
    // get user expire info
    public static function getUserExpireInfo(int $userId)
    {
        $userExpiredTime = User::where('id', $userId)->value('expired_at');

        $config['userStatus'] = PermissionUtility::checkUserStatusOfSiteMode($userId);
        $config['expireTime'] = $userExpiredTime;
        $config['expireAfter'] = ConfigHelper::fresnsConfigByItemKey('site_private_end_after');

        return $config;
    }

    // Get user main role permission
    public static function getUserMainRolePerm(int $userId)
    {
        $defaultRoleId = ConfigHelper::fresnsConfigByItemKey('default_role');
        $userRole = UserRole::where('user_id', $userId)->where('is_main', 1)->first();

        $roleId = $userRole->role_id ?? $defaultRoleId;
        $restoreRoleId = $userRole->restore_role_id ?? $defaultRoleId;
        $expireTime = strtotime($userRole->expired_at ?? null);
        $now = time();

        if (! empty($userRole) && $expireTime && $expireTime < $now) {
            $roleId = $restoreRoleId;
        }

        $rolePerm = Role::whereId($roleId)->isEnable()->value('permission');
        if (empty($rolePerm)) {
            $roleId = null;
            $rolePerm = Role::whereId($defaultRoleId)->isEnable()->value('permission') ?? [];
        }

        foreach ($rolePerm as $perm) {
            $permission['rid'] = $roleId;
            $permission[$perm['permKey']] = $perm['permValue'];
        }

        return $permission;
    }

    // Get group filter ids
    public static function getGroupFilterIds(?int $userId = null)
    {
        if (empty($userId)) {
            return [];
        }

        $followGroupIds = UserFollow::type(UserFollow::TYPE_GROUP)->where('user_id', $userId)->pluck('follow_id')->toArray();
        $groupIds = Group::where('type', 2)->where('type_find', 2)->pluck('id')->toArray();

        $filtered = array_values(array_diff($groupIds, $followGroupIds));

        return $filtered;
    }

    // Get group post filter ids
    public static function getGroupPostFilterIds(?int $userId = null)
    {
        if (empty($userId)) {
            return [];
        }

        $followGroupIds = UserFollow::type(UserFollow::TYPE_GROUP)->where('user_id', $userId)->pluck('follow_id')->toArray();
        $groupIds = Group::where('type', 2)->where('type_mode', 2)->pluck('id')->toArray();

        $filtered = array_values(array_diff($groupIds, $followGroupIds));

        return $filtered;
    }

    // Check if the user belongs to the account
    public static function checkUserAffiliation(int $userId, int $accountId): bool
    {
        $userAccountId = User::where('id', $userId)->value('account_id');

        return $userAccountId == $accountId ? 'true' : 'false';
    }

    // Check user status of the site mode
    public static function checkUserStatusOfSiteMode(int $userId): bool
    {
        $modeConfig = ConfigHelper::fresnsConfigByItemKey('site_mode');
        $userSet = User::where('id', $userId)->value('expired_at');

        if ($modeConfig == 'public') {
            return true;
        }

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

    // Check user dialog permission
    public static function checkUserDialogPerm(User $receiveUser, int $authUserId, ?string $langTag = null)
    {
        $configs = ConfigHelper::fresnsConfigByItemKeys(['dialog_status', 'dialog_files']);

        $info['status'] = $configs['dialog_status'];
        $info['files'] = $configs['dialog_files'];
        $info['code'] = 0;
        $info['message'] = 'ok';

        if (! $configs['dialog_status']) {
            $info['status'] = false;
            $info['code'] = 36600;
            $info['message'] = ConfigUtility::getCodeMessage(36600, 'Fresns', $langTag);

            return  $info;
        }

        if ($receiveUser->id == $authUserId) {
            $info['status'] = false;
            $info['code'] = 31602;
            $info['message'] = ConfigUtility::getCodeMessage(31602, 'Fresns', $langTag);

            return  $info;
        }

        if (! is_null($receiveUser->deleted_at)) {
            $info['status'] = false;
            $info['code'] = 35203;
            $info['message'] = ConfigUtility::getCodeMessage(35203, 'Fresns', $langTag);

            return  $info;
        }

        if (! $receiveUser->is_enable) {
            $info['status'] = false;
            $info['code'] = 35202;
            $info['message'] = ConfigUtility::getCodeMessage(35202, 'Fresns', $langTag);

            return  $info;
        }

        $authUserRolePerm = PermissionUtility::getUserMainRolePerm($receiveUser->id);
        if (! $authUserRolePerm['dialog']) {
            $info['status'] = false;
            $info['code'] = 36114;
            $info['message'] = ConfigUtility::getCodeMessage(36114, 'Fresns', $langTag);

            return  $info;
        }

        $checkBlock = InteractiveUtility::checkUserBlock(InteractiveUtility::TYPE_USER, $authUserId, $receiveUser->id);
        if ($receiveUser->dialog_limit == 4 || $checkBlock) {
            $info['status'] = false;
            $info['code'] = 36608;
            $info['message'] = ConfigUtility::getCodeMessage(36608, 'Fresns', $langTag);

            return  $info;
        }

        $checkFollow = InteractiveUtility::checkUserFollow(InteractiveUtility::TYPE_USER, $receiveUser->id, $authUserId);
        $authUserVerifiedStatus = User::where('id', $authUserId)->value('verified_status') ?? 0;
        if ($receiveUser->dialog_limit == 3 && ! $checkFollow && ! $authUserVerifiedStatus) {
            $info['status'] = false;
            $info['code'] = 36607;
            $info['message'] = ConfigUtility::getCodeMessage(36607, 'Fresns', $langTag);

            return  $info;
        }

        if ($receiveUser->dialog_limit == 2 && ! $checkFollow) {
            $info['status'] = false;
            $info['code'] = 36606;
            $info['message'] = ConfigUtility::getCodeMessage(36606, 'Fresns', $langTag);

            return  $info;
        }

        return  $info;
    }

    // Check if the user is a group administrator
    public static function checkUserGroupAdmin(int $groupId, int $userId)
    {
        $groupAdminArr = GroupAdmin::where('group_id', $groupId)->pluck('user_id')->toArray();

        return in_array($userId, $groupAdminArr) ? 'true' : 'false';
    }

    // Check if the user has group publishing permissions
    public static function checkUserGroupPublishPerm(int $groupId, ?int $userId = null)
    {
        $perm['allowPost'] = true;
        $perm['reviewPost'] = true;
        $perm['allowComment'] = true;
        $perm['reviewComment'] = true;
        $perms = $perm;

        return $perms;
    }

    // Check post allow
    public static function checkPostAllow(int $postId, int $userId): bool
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
}
