<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Content;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsGroups\FresnsGroups;
use App\Http\Fresns\FresnsGroups\FresnsGroupsConfig;
use App\Http\Fresns\FresnsGroups\FresnsGroupsService;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShields;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\Share\AmGlobal\GlobalService;
use Illuminate\Support\Facades\DB;

class FresnsGroupResource extends BaseAdminResource
{
    public function toArray($request)
    {

        // form 字段
        $formMap = FresnsGroupsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }

        $gid = $this->uuid;
        $type = $this->type;
        // dd($type);
        $parentId = $this->parent_id;
        $parentGroupInfo = FresnsGroups::where('id', $parentId)->first();
        // dd($parentGroupInfo);
        $parentId = $parentGroupInfo['uuid'] ?? '';
        $mid = GlobalService::getGlobalKey('member_id');
        // dd($mid);
        // 语言
        $name = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->id);
        // dd($name);
        $description = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'description', $this->id);
        $gname = $name == null ? '' : $name['lang_content'];
        $description = $description == null ? '' : $description['lang_content'];
        // dump($gname);
        // dd($description);
        // $cover = $this->cover_file_url;
        $cover = ApiFileHelper::getImageSignUrlByFileIdUrl($this->cover_file_id, $this->cover_file_url);
        // $banner = $this->banner_file_url;
        $banner = ApiFileHelper::getImageSignUrlByFileIdUrl($this->banner_file_id, $this->banner_file_url);
        $recommend = $this->is_recommend;
        $followType = $this->type_follow;
        $followUrl = $this->plugin_unikey;
        $viewCount = $this->view_count;
        $likeCount = $this->like_count;
        $followCount = $this->follow_count;
        $shieldCount = $this->shield_count;
        $postCount = $this->post_count;
        $essenceCount = $this->essence_count;
        // 是否关注
        // $followStatus = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->where('follow_id',$this->id)->count();
        $followStatus = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type',
            2)->where('follow_id', $this->id)->count();
        // 是否点赞
        // $likeStatus = FresnsMemberLikes::where('member_id',$mid)->where('like_type',2)->where('like_id',$this->id)->count();
        $likeStatus = DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('member_id', $mid)->where('like_type',
            2)->where('like_id', $this->id)->count();
        // 是否屏蔽
        // $shieldStatus = FresnsMemberShields::where('member_id',$mid)->where('shield_type',2)->where('shield_id',$this->id)->count();
        $shieldStatus = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id', $mid)->where('shield_type',
            2)->where('shield_id', $this->id)->count();
        // dd(1);
        $followSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::FOLLOW_GROUP_SETTING);
        $likeSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::LIKE_GROUP_SETTING);
        $shieldSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::SHIELD_SETTING);
        $groupName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::GROUP_NAME) ?? '小组';
        $followName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::GROUP_FOLLOW_NAME) ?? '加入';
        $likeName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::GROUP_LIKE_NAME) ?? '点赞';
        $shieldName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::GROUP_SHIELD_NAME) ?? '屏蔽';
        $parentGroup = FresnsGroups::find($this->parent_id);
        $parentInfo = [];
        if ($parentGroup) {
            $parentInfo['gid'] = $parentGroup['uuid'] ?? '';
            $pname = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->id);
            $parentInfo['gname'] = $pname == null ? '' : $pname['lang_content'];
            // $parentInfo['cover'] = $parentGroup['cover_file_url'] ?? "";
            $parentInfo['cover'] = ApiFileHelper::getImageSignUrlByFileIdUrl($parentGroup['cover_file_id'],
                $parentGroup['cover_file_url']);
        }
        $admins = [];
        if ($type != 1) {
            $admins = FresnsGroupsService::adminData($this->permission);
        }
        $publishRule = [];
        if ($type != 1) {
            $publishRule = FresnsGroupsService::publishRule($mid, $this->permission, $this->id);
        }
        $permission = [];
        if ($type != 1) {
            $permission = FresnsGroupsService::othetPession($this->permission);
        }

        // 默认字段
        $default = [
            'gid' => $gid,
            'gname' => $gname,
            'type' => $type,
            // 'parentId' => $parentId,
            'description' => $description,
            'cover' => $cover,
            'banner' => $banner,
            'recommend' => $recommend,
            'groupName' => $groupName,
            'followSetting' => $followSetting,
            'followName' => $followName,
            'followStatus' => $followStatus,
            'followType' => $followType,
            'followUrl' => $followUrl,
            'likeSetting' => $likeSetting,
            'likeName' => $likeName,
            'likeStatus' => $likeStatus,
            'shieldSetting' => $shieldSetting,
            'shieldName' => $shieldName,
            'shieldStatus' => $shieldStatus,
            'viewCount' => $viewCount,
            'likeCount' => $likeCount,
            'followCount' => $followCount,
            'shieldCount' => $shieldCount,
            'postCount' => $postCount,
            'essenceCount' => $essenceCount,
            'parentInfo' => $parentInfo,
            'admins' => $admins,
            'publishRule' => $publishRule,
            'permission' => $permission,
        ];
        // 合并
        $arr = $default;
        // dd($arr);
        return $arr;
    }
}
