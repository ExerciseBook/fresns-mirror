<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Messages;

use App\Base\Checkers\BaseChecker;
use App\Http\Center\Common\LogService;
use App\Http\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRelsService;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRolesService;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsUsers\FresnsUsers;

class AmChecker extends BaseChecker
{
    // Status Code
    const MEMBER_ROLE_ERROR = 30083;
    const MEMBER_ERROR = 30032;
    const MEMBER_FOLLOW_ERROR = 30033;
    const FILE_OR_MESSAGE_ERROR = 30031;
    const DIALOG_WORD_ERROR = 30036;
    const VERIFIED_ERROR = 30034;
    const MEMBER_ME_ERROR = 30089;
    const DIALOG_ERROR = 30028;
    const MEMBER_EXPIRED_ERROR = 30091;
    public $codeMap = [
        self::MEMBER_ROLE_ERROR => '该成员无发送消息权限',
        self::MEMBER_ERROR => '对方已注销',
        self::MEMBER_FOLLOW_ERROR => '需关注对方才能发送消息',
        self::FILE_OR_MESSAGE_ERROR => '文件和消息只能传其一',
        self::DIALOG_WORD_ERROR => '存在屏蔽字，禁止发送',
        self::VERIFIED_ERROR => '需认证才能给对方发消息',
        self::MEMBER_ME_ERROR => '自己不能给自己发送信息',
        self::DIALOG_ERROR => '已关闭私信功能，暂不能发送私信',
        self::MEMBER_EXPIRED_ERROR => '成员已过期，不能发送私信',
    ];

    public static function checkSendMessage($mid)
    {
        // return true;
        // Key Name dialog_status Configure global dialog function
        $dialogStatus = ApiConfigHelper::getConfigByItemKey(AmConfig::DIALOG_STATUS);
        if (! $dialogStatus) {
            return self::checkInfo(self::DIALOG_ERROR);
        }

        // In case of private mode, when expired (members > expired_at ) no messages are allowed to be sent.
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $memberInfo = FresnsMembers::find($mid);
            if ($memberInfo['expired_at'] && ($memberInfo['expired_at'] <= date('Y-m-d H:i:s'))) {
                LogService::info('Your account status has expired', $memberInfo);
                return self::checkInfo(self::MEMBER_EXPIRED_ERROR);
            }
        }

        // Determine if the member master role has the right to send private messages (member_roles > permission > dialog=true)
        $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($mid);
        if (empty($roleId)) {
            return self::checkInfo(self::MEMBER_ROLE_ERROR);
        }
        $memberRole = FresnsMemberRoles::where('id', $roleId)->first();
        if (! empty($memberRole)) {
            $permission = $memberRole['permission'];
            $permissionArr = json_decode($permission, true);
            if (! empty($permissionArr)) {
                $permissionMap = FresnsMemberRolesService::getPermissionMap($permissionArr);
                if (empty($permissionMap)) {
                    return self::checkInfo(self::MEMBER_ROLE_ERROR);
                }
            }
            if (! isset($permissionMap['dialog'])) {
                return self::checkInfo(self::MEMBER_ROLE_ERROR);
            }
            if ($permissionMap['dialog'] == false) {
                return self::checkInfo(self::MEMBER_ROLE_ERROR);
            }
        } else {
            return self::checkInfo(self::MEMBER_ROLE_ERROR);
        }

        // Determine if the other party has deleted (members > deleted_at)
        $recvMid = request()->input('recvMid');
        $recvMidInfo = FresnsMembers::where('uuid', $recvMid)->first();
        if (! $recvMidInfo) {
            return self::checkInfo(self::MEMBER_ERROR);
        }

        // Determine whether the dialog settings match each other (members > dialog_limit)
        $memberInfo = FresnsMembers::where('uuid', $recvMid)->first();
        if ($memberInfo['id'] == $mid) {
            return self::checkInfo(self::MEMBER_ME_ERROR);
        }
        // dialog_limit = 2 / Only members that I am allowed to follow
        if ($memberInfo['dialog_limit'] == 2) {
            $count = FresnsMemberFollows::where('member_id', $mid)->where('follow_type', 1)->where('follow_id',
                $memberInfo['id'])->count();
            if ($count == 0) {
                return self::checkInfo(self::MEMBER_FOLLOW_ERROR);
            }
        }
        // dialog_limit = 3 / Members I follow and members I have certified
        if ($memberInfo['dialog_limit'] == 3) {
            $count = FresnsMemberFollows::where('member_id', $mid)->where('follow_type', 1)->where('follow_id',
                $memberInfo['id'])->count();
            if ($count == 0) {
                return self::checkInfo(self::MEMBER_FOLLOW_ERROR);
            }
            $myInfo = FresnsMembers::find($mid);
            if ($myInfo['verified_status'] == 1) {
                return self::checkInfo(self::VERIFIED_ERROR);
            }
        }

        // request
        $message = request()->input('message', null);
        $fid = request()->input('fid', null);
        if ($message && $fid) {
            return self::checkInfo(self::FILE_OR_MESSAGE_ERROR);
        }
    }
}
