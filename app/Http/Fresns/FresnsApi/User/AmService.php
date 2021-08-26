<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\User;

use App\Helpers\DateHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsLanguages\FresnsLanguagesService;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRelsService;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRolesConfig;
use App\Http\Fresns\FresnsMembers\FresnsMembersConfig;
use App\Http\Fresns\FresnsPluginBadges\FresnsPluginBadgesService;
use App\Http\Fresns\FresnsPlugins\FresnsPluginsService;
use App\Http\Fresns\FresnsUserConnects\FresnsUserConnectsConfig;
use App\Http\Fresns\FresnsUsers\FresnsUsersConfig;
use App\Http\Fresns\FresnsUserWallets\FresnsUserWallets;
use Illuminate\Support\Facades\DB;

class AmService
{
    public function getUserInfo($uid, $langTag, $mid = null)
    {
        $langTag = ApiLanguageHelper::getLangTagByHeader();

        if (empty($mid)) {
            $mid = Db::table(FresnsMembersConfig::CFG_TABLE)->where('user_id', $uid)->value('id');
        }

        $users = DB::table(FresnsUsersConfig::CFG_TABLE)->where('id', $uid)->first();
        $phone = $users->phone ?? '';
        $email = $users->email ?? '';

        $data['uid'] = $users->uuid ?? '';
        $data['countryCode'] = $users->country_code ?? '';
        $data['purePhone'] = ApiCommonHelper::encryptPhone($users->pure_phone ?? '');
        $data['phone'] = ApiCommonHelper::encryptPhone($phone, 5, 6) ?? '';
        $data['email'] = ApiCommonHelper::encryptEmail($email) ?? '';
        //配置表 account_prove_service 关联的插件，插件 URL
        $proveSupportUnikey = ApiConfigHelper::getConfigByItemKey('account_prove_service');
        $proveSupportUrl = FresnsPluginsService::getPluginUrlByUnikey($proveSupportUnikey);
        $data['proveSupport'] = $proveSupportUrl;
        $data['verifyStatus'] = $users->prove_verify ?? '';
        $data['realname'] = ApiCommonHelper::encryptName($users->prove_realname) ?? '';
        $data['gender'] = $users->prove_gender ?? '';
        $data['idType'] = $users->prove_type ?? '';
        $data['idNumber'] = ApiCommonHelper::encryptIdNumber($users->prove_number, 1, -1) ?? '';
        $data['registerTime'] = DateHelper::asiaShanghaiToTimezone($users->created_at ?? '');
        $data['status'] = $users->is_enable ?? '';
        $data['deactivate'] = boolval($users->deleted_at ?? '');
        $data['deactivateTime'] = DateHelper::asiaShanghaiToTimezone($users->deleted_at ?? '');

        $connectsArr = DB::table(FresnsUserConnectsConfig::CFG_TABLE)->where('user_id', $uid)->get([
            'connect_id',
            'is_enable',
        ])->toArray();
        $itemArr = [];
        if ($connectsArr) {
            foreach ($connectsArr as $v) {
                $item = [];
                $item['id'] = $v->connect_id;
                $item['status'] = $v->is_enable;
                $itemArr[] = $item;
            }
        }

        $data['connects'] = $itemArr;
        //钱包
        $userWallets = FresnsUserWallets::where('user_id', $uid)->first();
        $wallets['status'] = $userWallets['is_enable'] ?? '';
        $wallets['balance'] = $userWallets['balance'] ?? '';
        $wallets['freezeAmount'] = $userWallets['freeze_amount'] ?? '';
        $wallets['bankName'] = $userWallets['bank_name'] ?? '';
        $wallets['swiftCode'] = $userWallets['swift_code'] ?? '';
        $wallets['bankAddress'] = $userWallets['bank_address'] ?? '';
        $wallets['bankAccount'] = '';
        if (! empty($userWallets)) {
            $wallets['bankAccount'] = ApiCommonHelper::encryptIdNumber($userWallets['bank_account'], 4, -2);
        }
        $wallets['bankStatus'] = $userWallets['bank_status'] ?? '';

        $wallets['payExpands'] = FresnsPluginBadgesService::getPluginExpand($mid, AmConfig::PLUGIN_USAGERS_TYPE_1,
            $langTag);

        $wallets['withdrawExpands'] = FresnsPluginBadgesService::getPluginExpand($mid, AmConfig::PLUGIN_USAGERS_TYPE_2,
            $langTag);
        $data['wallet'] = $wallets;
        $memberArr = DB::table('members')->where('user_id', $uid)->get()->toArray();

        $itemArr = [];
        foreach ($memberArr as $v) {
            $item = [];
            $item['mid'] = $v->uuid;
            $item['mname'] = $v->name;
            $item['nickname'] = $v->nickname;
            $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($v->id);
            $memberRole = FresnsMemberRoles::where('id', $roleId)->first();
            $item['nicknameColor'] = '';
            $item['roleName'] = '';
            $item['roleNameDisplay'] = '';
            $item['roleIcon'] = '';
            $item['roleIconDisplay'] = '';
            if ($memberRole) {
                $item['nicknameColor'] = $memberRole['nickname_color'];
                $item['roleName'] = FresnsLanguagesService::getLanguageByTableId(FresnsMemberRolesConfig::CFG_TABLE,
                    'name', $memberRole['id'], $langTag);
                $item['roleNameDisplay'] = $memberRole['is_display_name'];
                $item['roleIcon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberRole['icon_file_id'], $memberRole['icon_file_url']);
                $item['roleIconDisplay'] = $memberRole['icon_display_icon'];
            }

            $isPassword = false;
            if (! empty($v->password)) {
                $isPassword = true;
            }
            $item['password'] = $isPassword;

            if (empty($users->deleted_at)) {
                if (empty($v->avatar_file_url) && empty($v->avatar_file_id)) {
                    $defaultAvatar = ApiConfigHelper::getConfigByItemKey('default_avatar');
                    $memberAvatar = ApiFileHelper::getImageSignUrl($defaultAvatar);
                } else {
                    $memberAvatar = ApiFileHelper::getImageSignUrlByFileIdUrl($v->avatar_file_id, $v->avatar_file_url);
                }
            } else {
                $deactivateAvatar = ApiConfigHelper::getConfigByItemKey('deactivate_avatar');
                $memberAvatar = ApiFileHelper::getImageSignUrl($deactivateAvatar);
            }
            $item['avatar'] = $memberAvatar;
            $item['verifiedStatus'] = $v->verified_status;
            $item['verifiedIcon'] = $v->verified_file_url;
            $item['verifiedDesc'] = $v->verified_desc;
            $item['status'] = $v->is_enable;
            $item['deactivate'] = DateHelper::asiaShanghaiToTimezone($v->deleted_at);
            $item['deactivateTime'] = DateHelper::asiaShanghaiToTimezone($v->deleted_at);
            $item['multiple'] = '';
            $itemArr[] = $item;
        }
        $data['members'] = $itemArr;
        $data['memberName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE, 'item_value',
            'member_name', $langTag);
        $data['memberIdName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'member_id_name', $langTag);
        $data['memberNameName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'member_name_name', $langTag);
        $data['memberNicknameName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'member_nickname_name', $langTag);
        $data['memberRoleName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'member_role_name', $langTag);

        return $data;
    }
}
