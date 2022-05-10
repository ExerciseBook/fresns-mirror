<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Helpers;

use App\Models\Group;
use App\Models\User;
use App\Models\UserRole;

class UserHelper
{
    /**
     * Determine if the user belongs to the account.
     *
     * @param  int  $uid
     * @param  string  $aid
     * @return bool
     */
    public static function fresnsUserAffiliation(int $uid, string $aid)
    {
        $userAccountId = PrimaryHelper::fresnsAccountIdByUid($uid);
        $accountId = PrimaryHelper::fresnsAccountIdByAid($aid);

        return $userAccountId == $accountId ? 'true' : 'false';
    }

    /**
     * Whether the user is disabled or not.
     *
     * @param  int  $uid
     * @return bool
     */
    public static function fresnsUserStatus(int $uid)
    {
        $userStatus = User::where('uid', $uid)->value('is_enable');

        if (empty($userStatus)) {
            return 'false';
        }

        return $userStatus == 0 ? 'true' : 'false';
    }

    /**
     * Determining user role permission.
     *
     * @param  int  $uid
     * @param  array  $permRoleIds
     * @return bool
     */
    public static function fresnsUserRolePermCheck(int $uid, array $permRoleIds)
    {
        $userId = PrimaryHelper::fresnsAccountIdByUid($uid);
        $userRoles = UserRole::where('user_id', $userId)->pluck('role_id')->toArray();

        return array_intersect($permRoleIds, $userRoles) ? 'true' : 'false';
    }

    /**
     * @param  int  $uid
     * @param  string  $gid
     * @return bool
     */
    public static function fresnsUserGroupAdminCheck(int $uid, string $gid)
    {
        $permission = Group::where('gid', $gid)->value('permission');
        $permissionArr = json_decode($permission, true);
        $isAdmin = UserHelper::fresnsUserRolePermCheck($uid, $permissionArr['admin_users']);

        return $isAdmin;
    }

    public static function fresnsUserAnonymousProfile()
    {
        $anonymousAvatar = ConfigHelper::fresnsConfigByItemKey('anonymous_avatar');
        $userAvatar = null;
        if (ConfigHelper::fresnsConfigFileValueTypeByItemKey('anonymous_avatar') == 'URL') {
            $userAvatar = $anonymousAvatar;
        } else {
            $fresnsResp = \FresnsCmdWord::plugin('Fresns')->getFileUrlOfAntiLink([
                'fileId' => $anonymousAvatar,
            ]);
            $userAvatar = $fresnsResp->getData('imageAvatarUrl');
        }

        $profile['uid'] = null;
        $profile['username'] = null;
        $profile['nickname'] = null;
        $profile['avatar'] = $userAvatar;
        $profile['decorate'] = null;
        $profile['gender'] = null;
        $profile['birthday'] = null;
        $profile['bio'] = null;
        $profile['location'] = null;
        $profile['dialogLimit'] = null;
        $profile['commentLimit'] = null;
        $profile['timezone'] = null;
        $profile['verifiedStatus'] = null;
        $profile['verifiedIcon'] = null;
        $profile['verifiedDesc'] = null;
        $profile['verifiedDateTime'] = null;
        $profile['expiryDateTime'] = null;
        $profile['lastPublishPost'] = null;
        $profile['lastPublishComment'] = null;
        $profile['lastEditUsername'] = null;
        $profile['lastEditNickname'] = null;
        $profile['registerDateTime'] = null;
        $profile['hasPassword'] = null;
        $profile['status'] = 1;
        $profile['deactivate'] = false;
        $profile['deactivateTime'] = null;

        $profile['nicknameColor'] = null;
        $profile['rid'] = null;
        $profile['roleName'] = null;
        $profile['roleNameDisplay'] = 0;
        $profile['roleIcon'] = null;
        $profile['roleIconDisplay'] = 0;
        $profile['roleExpiryDateTime'] = null;
        $profile['rolePermission'] = null;

        return $profile;
    }
}
