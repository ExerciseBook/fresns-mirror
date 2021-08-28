<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Member;

use App\Base\Resources\BaseAdminResource;
use App\Helpers\DateHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsLanguages\FresnsLanguagesService;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberIcons\FresnsMemberIcons;
use App\Http\FresnsDb\FresnsMemberIcons\FresnsMemberIconsConfig;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRelsService;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRolesConfig;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShields;
use App\Http\FresnsDb\FresnsMemberStats\FresnsMemberStats;

class FresnsMemberListsResource extends BaseAdminResource
{
    public function toArray($request)
    {
        $langTag = request()->input('langTag');
        $mid = request()->input('mid');
        $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($this->id);
        $memberRole = FresnsMemberRoles::where('id', $roleId)->first();
        $memberRole = FresnsMemberRoles::where('id', $roleId)->first();
        $nicknameColor = '';
        $roleName = '';
        $roleNameDisplay = '';
        $roleIcon = '';
        $roleIconDisplay = '';
        if ($memberRole) {
            $nicknameColor = $memberRole['nickname_color'];
            $roleName = FresnsLanguagesService::getLanguageByTableId(FresnsMemberRolesConfig::CFG_TABLE,
                'name', $memberRole['id'], $langTag);
            $roleNameDisplay = $memberRole['is_display_name'];
            $roleIcon = ApiFileHelper::getImageSignUrlByFileIdUrl($memberRole['icon_file_id'], $memberRole['icon_file_url']);
            $roleIconDisplay = $memberRole['icon_display_icon'];
        }
        $follows = FresnsMemberFollows::where('member_id', $mid)->where('follow_type', 1)->where('follow_id',
            $this->id)->first();
        $isFollows = 0;
        if (empty($follows)) {
            $follows = FresnsMemberFollows::where('member_id', $this->id)->where('follow_type', 1)->where('follow_id',
                $mid)->first();
            if ($follows) {
                $isFollows = 2;
            }
        } else {
            if ($follows['is_mutual'] == 1) {
                $isFollows = 3;
            } else {
                $isFollows = 1;
            }
        }

        $isLike = 0;
        $count = FresnsMemberLikes::where('member_id', $mid)->where('like_type', 1)->where('like_id',
            $this->id)->count();
        if ($count > 0) {
            $isLike = 1;
        }
        $isShields = 0;
        $count = FresnsMemberShields::where('member_id', $mid)->where('shield_type', 1)->where('shield_id',
            $this->id)->count();
        if ($count > 0) {
            $isShields = 1;
        }
        $memberStats = FresnsMemberStats::where('member_id', $this->id)->first();
        $stats['likeMemberCount'] = $memberStats['like_member_count'] ?? 0;
        $stats['followMemberCount'] = $memberStats['follow_member_count'] ?? 0;
        $stats['shieldMemberCount'] = $memberStats['shield_member_count'] ?? 0;
        $stats['postPublishCount'] = $memberStats['post_publish_count'] ?? 0;
        $stats['postLikeCount'] = $memberStats['post_like_count'] ?? 0;
        $stats['commentPublishCount'] = $memberStats['comment_publish_count'] ?? 0;
        $stats['commentLikeCount'] = $memberStats['comment_like_count'] ?? 0;
        $stats['extcredits1Status'] = ApiConfigHelper::getConfigByItemKey('extcredits1_status');
        $stats['extcredits1Name'] = ApiConfigHelper::getConfigByItemKey('extcredits1_name');
        $stats['extcredits1Unit'] = ApiConfigHelper::getConfigByItemKey('extcredits1_unit');
        // if($stats['extcredits1Status'] == 3){
        $stats['extcredits1'] = $memberStats['extcredits1'] ?? '';
        // }
        $stats['extcredits2Status'] = ApiConfigHelper::getConfigByItemKey('extcredits2_status');
        $stats['extcredits2Name'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'extcredits2_name', $langTag);
        $stats['extcredits2Unit'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'extcredits2_unit', $langTag);
        // if($stats['extcredits2Status'] == 3){
        $stats['extcredits2'] = $memberStats['extcredits2'] ?? '';
        // }
        $stats['extcredits3Status'] = ApiConfigHelper::getConfigByItemKey('extcredits3_status');
        $stats['extcredits3Name'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'extcredits3_name', $langTag);
        $stats['extcredits3Unit'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'extcredits3_unit', $langTag);
        // if($stats['extcredits3Status'] == 3){
        $stats['extcredits3'] = $memberStats['extcredits3'] ?? '';
        // }
        $stats['extcredits4Status'] = ApiConfigHelper::getConfigByItemKey('extcredits4_status');
        $stats['extcredits4Name'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'extcredits4_name', $langTag);
        $stats['extcredits4Unit'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'extcredits4_unit', $langTag);
        // if($stats['extcredits4Status'] == 3){
        $stats['extcredits4'] = $memberStats['extcredits4'] ?? '';
        // }
        $stats['extcredits5Status'] = ApiConfigHelper::getConfigByItemKey('extcredits5_status');
        $stats['extcredits5Name'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'extcredits5_name', $langTag);
        $stats['extcredits5Unit'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
            'item_value', 'extcredits5_unit', $langTag);
        // if($stats['extcredits5Status'] == 3){
        $stats['extcredits5'] = $memberStats['extcredits5'] ?? '';
        // }

        $memberIconsArr = FresnsMemberIcons::where('member_id', $this->id)->get()->toArray();
        $iconsArr = [];
        foreach ($memberIconsArr as $v) {
            $item = [];
            $item['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($v['icon_file_id'], $v['icon_file_url']);
            $item['name'] = FresnsLanguagesService::getLanguageByTableId(FresnsMemberIconsConfig::CFG_TABLE, 'name',
                $v['id'], $langTag);
            $iconsArr[] = $item;
        }

        if (empty($this->avatar_file_url) && empty($this->avatar_file_id)) {
            $defaultAvatar = ApiConfigHelper::getConfigByItemKey('default_avatar');
            $memberAvatar = ApiFileHelper::getImageSignUrl($defaultAvatar);
        } else {
            $memberAvatar = ApiFileHelper::getImageSignUrlByFileIdUrl($this->avatar_file_id, $this->avatar_file_url);
        }

        // 默认字段
        $default = [
            'mid' => $this->uuid,
            'mname' => $this->name,
            'nickname' => $this->nickname,
            'nicknameColor' => $nicknameColor,
            'roleName' => $roleName,
            'roleNameDisplay' => $roleNameDisplay,
            'roleIcon' => $roleIcon,
            'roleIconDisplay' => $roleIconDisplay,
            'avatar' => $memberAvatar,
            'decorate' => ApiFileHelper::getImageSignUrlByFileIdUrl($this->decorate_file_id, $this->decorate_file_url),
            'gender' => $this->gender,
            'birthday' => DateHelper::asiaShanghaiToTimezone($this->birthday),
            'bio' => $this->bio,
            'followSetting' => ApiConfigHelper::getConfigByItemKey('follow_member_setting'),
            'followName' => FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                'follow_member_name', $langTag),
            'followStatus' => $isFollows,
            'likeSetting' => ApiConfigHelper::getConfigByItemKey('like_member_setting'),
            'likeName' => FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                'like_member_name', $langTag),
            'likeStatus' => $isLike,
            'shieldSetting' => ApiConfigHelper::getConfigByItemKey('shield_member_setting'),
            'shieldName' => FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                'shield_member_name', $langTag),
            'shieldStatus' => $isShields,
            'verifiedStatus' => $this->verified_status,
            'verifiedIcon' => ApiFileHelper::getImageSignUrlByFileIdUrl($this->verified_file_id,
                $this->verified_file_url),
            'stats' => $stats,
            'icons' => $iconsArr,
        ];

        return $default;
    }
}
