<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Notify;

use App\Base\Checkers\BaseChecker;
use App\Http\Fresns\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsFiles\FresnsFiles;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRelsService;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRolesService;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsUsers\FresnsUsers;
use App\Http\Models\Common\ConfigGroup;
use App\Http\Share\Common\LogService;

class AmChecker extends BaseChecker
{
    // 错误码
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
        // 键名 dialog_status 关闭了总站私信功能，全员不可发送。
        $dialogStatus = ApiConfigHelper::getConfigByItemKey(AmConfig::DIALOG_STATUS);
        // dd($dialogStatus);
        if (! $dialogStatus) {
            return self::checkInfo(self::DIALOG_ERROR);
        }
        // 如果是私有模式，当过期后 members > expired_at ，不允许发送消息。
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        // dump($site_mode);
        if ($site_mode == AmConfig::PRIVATE) {
            $memberInfo = FresnsMembers::find($mid);
            if ($memberInfo['expired_at'] && ($memberInfo['expired_at'] <= date('Y-m-d H:i:s'))) {
                LogService::info('私有模式有效期过期', $memberInfo);

                return self::checkInfo(self::MEMBER_EXPIRED_ERROR);
            }
        }
        // 需要先判断成员主角色是否有权发送私信（member_roles > permission > dialog=true）
        // dump($mid);
        $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($mid);
        // dd($roleId);
        if (empty($roleId)) {
            return self::checkInfo(self::MEMBER_ROLE_ERROR);
        }
        $memberRole = FresnsMemberRoles::where('id', $roleId)->first();
        // dd($memberRole);
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
        // $fresnsMemberRoles = FresnsMemberRoleRels::where('member_id',$mid)->where('type',2)->first();
        // dd($fresnsMemberRoles);
        // if(!$fresnsMemberRoles){
        //     // $this->error(ErrorCodeService::MEMBER_ROLE_ERROR);
        //     LogService::info('角色是否无权发送私信',$fresnsMemberRoles);
        //     return self::checkInfo(self::MEMBER_ROLE_ERROR);
        // }else{
        //     $memberRoles = FresnsMemberRoles::find($fresnsMemberRoles['role_id']);
        //     // dd($memberRoles);
        //     if(!$memberRoles){
        //         // $this->error(ErrorCodeService::MEMBER_ROLE_ERROR);
        //         return self::checkInfo(self::MEMBER_ROLE_ERROR);

        //     }else{
        //         if(!$memberRoles['permission']){
        //             return self::checkInfo(self::MEMBER_ROLE_ERROR);
        //         }else{
        //             $permission = json_decode($memberRoles['permission'],true);
        //             // dd($permission);
        //             if(!isset($permission[0])){
        //                 return self::checkInfo(self::MEMBER_ROLE_ERROR);
        //             }

        //             if(!isset($permission[0]['dialog'])){
        //                 return self::checkInfo(self::MEMBER_ROLE_ERROR);
        //             }
        //             if($permission[0]['dialog'] == 1){
        //                 return self::checkInfo(self::MEMBER_ROLE_ERROR);
        //             }
        //         }
        //     }
        // }

        // 如果对方已经注销（members > deleted_at），不可以发送。
        $recvMid = request()->input('recvMid');
        $recvMidInfo = FresnsMembers::where('uuid', $recvMid)->first();
        if (! $recvMidInfo) {
            // $this->error(ErrorCodeService::MEMBER_ERROR);
            return self::checkInfo(self::MEMBER_ERROR);
        }
        // 符合对方的私信设置（members > dialog_limit）
        $memberInfo = FresnsMembers::where('uuid', $recvMid)->first();
        if ($memberInfo['id'] == $mid) {
            return self::checkInfo(self::MEMBER_ME_ERROR);
        }
        if ($memberInfo['dialog_limit'] == 2) {
            $count = FresnsMemberFollows::where('member_id', $mid)->where('follow_type', 1)->where('follow_id',
                $memberInfo['id'])->count();
            if ($count == 0) {
                // $this->error(ErrorCodeService::MEMBER_FOLLOW_ERROR);
                return self::checkInfo(self::MEMBER_FOLLOW_ERROR);
            }
        }
        // 我关注的人和已认证的人（verified_status）
        if ($memberInfo['dialog_limit'] == 3) {
            $count = FresnsMemberFollows::where('member_id', $mid)->where('follow_type', 1)->where('follow_id',
                $memberInfo['id'])->count();
            if ($count == 0) {
                return self::checkInfo(self::MEMBER_FOLLOW_ERROR);
            }
            // 自己的信息
            $myInfo = FresnsMembers::find($mid);
            if ($myInfo['verified_status'] == 1) {
                return self::checkInfo(self::VERIFIED_ERROR);
            }
        }
        // dd($mid);
        $message = request()->input('message', null);
        $fid = request()->input('fid', null);
        if ($message && $fid) {
            // $this->error(ErrorCodeService::FILE_OR_MESSAGE_ERROR);
            return self::checkInfo(self::FILE_OR_MESSAGE_ERROR);
        }
    }
}
