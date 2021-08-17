<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Content;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsGroups\FresnsGroupsConfig;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;
use App\Http\Fresns\FresnsPluginBadges\FresnsPluginBadges;
use App\Http\Fresns\FresnsApi\Info\AmService;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShields;
use App\Http\Fresns\FresnsGroups\FresnsGroups;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsGroups\FresnsGroupsService;
use Illuminate\Support\Facades\DB;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsagesService;
use App\Http\Share\AmGlobal\GlobalService;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;

class FresnsGroupResourceDetail extends BaseAdminResource
{

    public function toArray($request)
    {

        // dd(1);
        // form 字段
        $formMap = FresnsGroupsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        // seoInfo

        $mid = GlobalService::getGlobalKey('member_id');
        $gid = $this->uuid;
        $type = $this->type;
        $parentId = $this->parent_id;
        $langTag = request()->header('langTag');
        // 语言
        $name = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->id);
        $description = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'description', $this->id);
        $gname = $name == null ? "" : $name['lang_content'];
        $description = $description == null ? "" : $description['lang_content'];
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

        $followSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::FOLLOW_GROUP_SETTING);
        $likeSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::LIKE_GROUP_SETTING);
        $shieldSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::SHIELD_SETTING);
        $groupName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::GROUP_NAME) ?? "小组";
        $followName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::GROUP_FOLLOW_NAME) ?? "加入";
        $likeName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::GROUP_LIKE_NAME) ?? "点赞";
        $shieldName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::GROUP_SHIELD_NAME) ?? "屏蔽";
        $extends = [];
        // $extends['plugin'] = "";
        // $extends['name'] = "";
        // $extends['icon'] = "";
        // $extends['url'] = "";
        // $extends['badgesType'] = "";
        // $extends['badgesValue'] = "";
        // $pluginUsages = FresnsPluginUsages::where('type',6)->where('group_id',$this->id)->first();
        // // dd($pluginUsages);
        // if($pluginUsages){
        //    $plugin = FresnsPlugin::where('unikey',$pluginUsages['plugin_unikey'])->first();
        //    $pluginBadges = FresnsPluginBadges::where('plugin_unikey',$pluginUsages['plugin_unikey'])->first();
        //    $extends['plugin'] = $pluginUsages['plugin_unikey'] ?? "";
        //    $name = AmService::getlanguageField('name',$pluginUsages['id']);
        //    $extends['name'] = $name == null ?"":$name['lang_content'];
        //    $extends['icon'] = $pluginUsages['icon_file_url'] ?? "";
        // //    dump($plugin);
        // //    dump($pluginBadges);
        //    $extends['url'] = $plugin['access_path '] . $pluginUsages['parameter'];
        //    $extends['badgesType'] = $pluginBadges['display_type'] ?? "";
        //    $extends['badgesValue'] = $pluginBadges['value_text'] ?? $pluginBadges['value_number'];
        //    // 是否有权限
        //    if($pluginUsages['member_roles']){
        //     $member_roles = $pluginUsages['member_roles'];
        //     // dump($member_roles);
        //     // dump($mid);
        //     $memberRoleArr = FresnsMemberRoleRels::where('member_id',$mid)->pluck('role_id')->toArray();
        //     $memberPluginRolesArr = explode(',',$member_roles);
        //     $status = array_intersect($memberRoleArr,$memberPluginRolesArr);
        //     if(empty($status)){
        //         $extends = [];
        //     }
        // }
        // }
        $parentInfo = [];
        $parentGroup = FresnsGroups::find($this->parent_id);
        if ($parentGroup) {
            $parentInfo['gid'] = $parentGroup['uuid'] ?? "";
            $pname = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->id);
            $parentInfo['pname'] = $pname == null ? "" : $pname['lang_content'];
            $parentInfo['cover'] = $parentGroup['cover_file_url'] ?? "";
        }
        $parentInfo = [];
        if ($parentGroup) {
            $parentInfo['gid'] = $parentGroup['uuid'] ?? "";
            $pname = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->id);
            $parentInfo['gname'] = $pname == null ? "" : $pname['lang_content'];
            $parentInfo['cover'] = ApiFileHelper::getImageSignUrlByFileIdUrl($parentGroup['cover_file_id'],
                $parentGroup['cover_file_url']);
        }
        $admins = [];
        // dd($type);
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
        // seoInfo
        FresnsGroups::where('id', $this->id)->increment('view_count');
        // 默认字段
        $default = [
            'gid' => $gid,
            // 'type' => $type,
            // 'parentId' => $parentId,
            'gname' => $gname,
            'description' => $description,
            'cover' => $cover,
            'banner' => $banner,
            'mode' => $this->type_mode,
            'find' => $this->type_find,
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
            'followSetting' => $followSetting,
            'followName' => $followName,
            'likeName' => $likeName,
            'shieldName' => $shieldName,
            'parentInfo' => $parentInfo,
            'admins' => $admins,
            'publishRule' => $publishRule,
            'permission' => $permission,
            // 'extends' => $extends,
            // 'seoInfo' => $seoInfo
        ];
        // 合并
        $arr = $default;

        return $arr;
    }
}

