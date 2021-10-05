<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Content;

use App\Base\Resources\BaseAdminResource;
use App\Http\Center\Common\GlobalService;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsApi\Info\FsService;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsConfig;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsService;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShields;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\FresnsDb\FresnsPluginBadges\FresnsPluginBadges;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsagesService;
use Illuminate\Support\Facades\DB;

/**
 * Detail resource config handle
 */

class FresnsGroupsResourceDetail extends BaseAdminResource
{
    public function toArray($request)
    {
        // Form Field
        $formMap = FresnsGroupsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }

        // Group Info
        $mid = GlobalService::getGlobalKey('member_id');
        $gid = $this->uuid;
        $type = $this->type;
        $parentId = $this->parent_id;
        $langTag = request()->header('langTag');
        $name = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->id);
        $description = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'description', $this->id);
        $gname = $name == null ? '' : $name['lang_content'];
        $description = $description == null ? '' : $description['lang_content'];
        $cover = ApiFileHelper::getImageSignUrlByFileIdUrl($this->cover_file_id, $this->cover_file_url);
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
        
        // Operation behavior status
        $likeStatus = DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('member_id', $mid)->where('like_type', 2)->where('like_id', $this->id)->where('deleted_at',null)->count();
        $followStatus = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type', 2)->where('follow_id', $this->id)->where('deleted_at',null)->count();
        $shieldStatus = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id', $mid)->where('shield_type', 2)->where('shield_id', $this->id)->where('deleted_at',null)->count();
        // Operation behavior settings
        $likeSetting = ApiConfigHelper::getConfigByItemKey(FsConfig::LIKE_GROUP_SETTING);
        $followSetting = ApiConfigHelper::getConfigByItemKey(FsConfig::FOLLOW_GROUP_SETTING);
        $shieldSetting = ApiConfigHelper::getConfigByItemKey(FsConfig::SHIELD_GROUP_SETTING);
        // Operation behavior naming
        $likeName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', FsConfig::LIKE_GROUP_NAME) ?? 'Like';
        $followName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', FsConfig::FOLLOW_GROUP_NAME) ?? 'Join';
        $shieldName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', FsConfig::SHIELD_GROUP_NAME) ?? 'Block';
        // Content Naming
        $groupName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', FsConfig::GROUP_NAME) ?? 'Group';

        $extends = [];
        $parentInfo = [];
        $parentGroup = FresnsGroups::find($this->parent_id);
        if ($parentGroup) {
            $parentInfo['gid'] = $parentGroup['uuid'] ?? '';
            $pname = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->id);
            $parentInfo['pname'] = $pname == null ? '' : $pname['lang_content'];
            $parentInfo['cover'] = $parentGroup['cover_file_url'] ?? '';
        }
        $parentInfo = [];
        if ($parentGroup) {
            $parentInfo['gid'] = $parentGroup['uuid'] ?? '';
            $pname = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->id);
            $parentInfo['gname'] = $pname == null ? '' : $pname['lang_content'];
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

        FresnsGroups::where('id', $this->id)->increment('view_count');

        // Default Field
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

        // Merger
        $arr = $default;

        return $arr;
    }
}
