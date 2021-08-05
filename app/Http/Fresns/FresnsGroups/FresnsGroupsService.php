<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

// 系统解耦, 快捷方式入口
namespace App\Http\Fresns\FresnsGroups;

use App\Http\Fresns\FresnsApi\Base\FresnsBaseService;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsagesService;
use Illuminate\Support\Facades\DB;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin as pluginUnikey;
use App\Http\Fresns\FresnsPluginBadges\FresnsPluginBadges;
use App\Http\Fresns\FresnsApi\Info\AmService as InfoService;
use App\Http\Share\AmGlobal\GlobalService;
use App\Http\Fresns\FresnsApi\Content\AmConfig as ContentConfig;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;

class FresnsGroupsService extends FresnsBaseService
{
    public $needCommon = true;

    public function __construct()
    {
        $this->config = new AmConfig();
        $this->model = new AmModel();
        $this->resource = AmResource::class;
        $this->resourceDetail = AmResourceDetail::class;
    }

    public function common()
    {
        // $common =  parent::common();
        $id = request()->input('gid');
        $langTag = request()->header('langTag');
        $mid = GlobalService::getGlobalKey('member_id');
        $group = FresnsGroups::where('uuid', $id)->first();
        $common['seoInfo'] = [];
        if (!$langTag) {
            $langTag = FresnsPluginUsagesService::getDefaultLanguage();
        }
        if ($group) {
            $seo = DB::table('seo')->where('linked_type', 2)->where('linked_id', $group['id'])->where('lang_tag',
                $langTag)->where('deleted_at', null)->first();
            $seoInfo = [];
            if ($seo) {
                $seoInfo['title'] = $seo->title;
                $seoInfo['keywords'] = $seo->keywords;
                $seoInfo['description'] = $seo->description;
                $common['seoInfo'] = $seoInfo;
            }
        }

        $extends = [];
        // $extends['plugin'] = "";
        // $extends['name'] = "";
        // $extends['icon'] = "";
        // $extends['url'] = "";
        // $extends['badgesType'] = "";
        // $extends['badgesValue'] = "";
        // dd($group);
        if ($group) {
            $pluginUsages = FresnsPluginUsages::where('type', 6)->where('group_id', $group['id'])->first();
            if ($pluginUsages) {
                $plugin = pluginUnikey::where('unikey', $pluginUsages['plugin_unikey'])->first();
                //    dd($plugin);
                $pluginBadges = FresnsPluginBadges::where('plugin_unikey', $pluginUsages['plugin_unikey'])->first();
                $extends['plugin'] = $pluginUsages['plugin_unikey'] ?? "";
                $name = InfoService::getlanguageField('name', $pluginUsages['id']);
                $extends['name'] = $name == null ? "" : $name['lang_content'];
                //    $extends['icon'] = $pluginUsages['icon_file_url'] ?? "";
                $extends['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($pluginUsages['icon_file_id'],
                    $pluginUsages['icon_file_url']);
                //    $extends['url'] = $plugin['access_path']  .'/'. $pluginUsages['parameter'];
                $extends['url'] = ApiFileHelper::getPluginUsagesUrl($pluginUsages['plugin_unikey'],
                    $pluginUsages['id']);
                $extends['badgesType'] = $pluginBadges['display_type'] ?? "";
                $extends['badgesValue'] = ($pluginBadges['value_text'] ?? "") ?? ($pluginBadges['value_number'] ?? "");
                // 是否有权限
                if ($pluginUsages['member_roles']) {
                    $member_roles = $pluginUsages['member_roles'];
                    $memberRoleArr = FresnsMemberRoleRels::where('member_id', $mid)->pluck('role_id')->toArray();
                    $memberPluginRolesArr = explode(',', $member_roles);
                    $status = array_intersect($memberRoleArr, $memberPluginRolesArr);
                    if (!$status) {
                        $extends = [];
                    }
                }
            }
        }
        $common['extensions'] = $extends;
        return $common;
        // $extends = [];
        // $seoGroup['extends'] = $extends;
    }

    // 权限数据
    public static function publishRule($mid, $permission, $group_id)
    {
        $permissionArr = json_decode($permission, true);
        // dd($permissionArr);
        $admin_member = $permissionArr['admin_members'];
        $publish_post = $permissionArr['publish_post'];
        $publish_post_roles = $permissionArr['publish_post_roles'];
        $publish_post_review = $permissionArr['publish_post_review'];
        $publish_comment = $permissionArr['publish_comment'];
        $publish_comment_roles = $permissionArr['publish_comment_roles'];
        $publish_comment_review = $permissionArr['publish_comment_review'];
        // dd($permissionArr);
        $adminMemberArr = [];
        if ($admin_member) {
            foreach ($admin_member as $a) {
                $array = [];
                $memberInfo = FresnsMembers::find($a);
                if ($memberInfo) {
                    $array['mid'] = $memberInfo['uuid'];
                    $array['mname'] = $memberInfo['name'];
                    $array['nickname'] = $memberInfo['nickname'];
                    $array['nicknameColor'] = $memberInfo['uuid'];
                    // 成员角色关联表表
                    $roleRels = FresnsMemberRoleRels::where('member_id', $memberInfo['id'])->first();
                    if (!empty($roleRels)) {
                        $memberRole = FresnsMemberRoles::find($roleRels['role_id']);
                    }
                    $array['nicknameColor'] = $memberRole['nickname_color'] ?? "";
                    $array['avatar'] = $memberInfo['avatar_file_url'];
                    $adminMemberArr[] = $array;
                }
            }
        }
        $arr['adminMemberArr'] = $adminMemberArr;

        // 当前请求接口的成员，是否拥有该小组发表帖子权限
        $publishRule = [];
        $publishRule['allowPost'] = false;
        // 1.所有人
        if ($publish_post == 1) {
            $publishRule['allowPost'] = true;
        }
        //  2.仅关注了小组的成员
        if ($publish_post == 2) {
            $followCount = FresnsMemberFollows::where('member_id', $mid)->where('follow_type', 2)->where('follow_id',
                $group_id)->count();
            if ($followCount > 0) {
                $publishRule['allowPost'] = true;
            }
        }
        // 3.仅指定的角色成员
        if ($publish_post == 3) {
            $memberRoleArr = FresnsMemberRoleRels::where('member_id', $mid)->pluck('role_id')->toArray();
            $arrIntersect = array_intersect($memberRoleArr, $publish_post_roles);
            if ($arrIntersect) {
                $publishRule['allowPost'] = true;
            }
        }
        // 当前请求接口的成员，发帖是否需要审核（如果是管理员，无需审核）
        $publishRule['reviewPost'] = true;
        if ($publish_post_review == 0) {
            $publishRule['reviewPost'] = false;
        }
        // dd($admin_member);
        if ($admin_member) {
            if (in_array($mid, $admin_member)) {
                $publishRule['reviewPost'] = false;
            }
        }

        $publishRule['allowComment'] = false;
        // 1.所有人
        if ($publish_comment == 1) {
            $publishRule['allowComment'] = true;
        }
        //  2.仅关注了小组的成员
        if ($publish_comment == 2) {
            $followCount = FresnsMemberFollows::where('member_id', $mid)->where('follow_type', 2)->where('follow_id',
                $group_id)->count();
            if ($followCount > 0) {
                $publishRule['allowComment'] = true;
            }
        }
        // 3.仅指定的角色成员
        if ($publish_comment == 3) {
            $memberRoleArr = FresnsMemberRoleRels::where('member_id', $mid)->pluck('role_id')->toArray();
            $arrIntersect = array_intersect($memberRoleArr, $publish_comment_roles);
            if ($arrIntersect) {
                $publishRule['allowComment'] = true;
            }
        }
        $publishRule['reviewComment'] = true;
        if ($publish_comment_review == 0) {
            $publishRule['reviewComment'] = false;
        }
        if ($admin_member) {
            if (in_array($mid, $admin_member)) {
                $publishRule['reviewComment'] = false;
            }
        }
        return $publishRule;
    }

    // 管理员数据
    public static function adminData($permission)
    {
        $permissionArr = json_decode($permission, true);
        // dd($permissionArr);
        $admin_member = $permissionArr['admin_members'];
        // dump($admin_member);
        $publish_post = $permissionArr['publish_post'];
        $publish_post_roles = $permissionArr['publish_post_roles'];
        $publish_post_review = $permissionArr['publish_post_review'];
        $publish_comment = $permissionArr['publish_comment'];
        $publish_comment_roles = $permissionArr['publish_comment_roles'];
        $publish_comment_review = $permissionArr['publish_comment_review'];
        // dd($permissionArr);
        $adminMemberArr = [];
        if ($admin_member) {
            foreach ($admin_member as $a) {
                $array = [];
                $memberInfo = FresnsMembers::find($a);
                if ($memberInfo) {
                    $array['mid'] = $memberInfo['uuid'];
                    $array['mname'] = $memberInfo['name'];
                    $array['nickname'] = $memberInfo['nickname'];
                    $array['nicknameColor'] = $memberInfo['uuid'];
                    // 成员角色关联表表
                    $roleRels = FresnsMemberRoleRels::where('member_id', $memberInfo['id'])->first();
                    if (!empty($roleRels)) {
                        $memberRole = FresnsMemberRoles::find($roleRels['role_id']);
                    }
                    $array['nicknameColor'] = $memberRole['nickname_color'] ?? "";
                    // $array['avatar'] = $memberInfo['avatar_file_url'];
                    $avatar = $memberInfo['avatar_file_url'] ?? "";
                    // 为空用默认头像
                    if (empty($avatar)) {
                        $defaultIcon = ApiConfigHelper::getConfigByItemKey(ContentConfig::DEFAULT_AVATAR);
                        $avatar = $defaultIcon;
                    }
                    $avatar = ApiFileHelper::getImageSignUrl($avatar);
                    $array['avatar'] = $avatar;
                    $adminMemberArr[] = $array;
                }
            }
        }
        return $adminMemberArr;
    }

    // p其他pession
    public static function othetPession($permission)
    {
        $permissionArr = json_decode($permission, true);
        $arr = [];
        if (!$permissionArr) {
            return $arr;
        }
        unset($permissionArr['admin_members']);
        unset($permissionArr['publish_post']);
        // unset($permissionArr['publish_post_roles']);
        unset($permissionArr['publish_post_review']);
        unset($permissionArr['publish_comment']);
        // unset($permissionArr['publish_comment_roles']);
        unset($permissionArr['publish_comment_review']);
        return $permissionArr;
    }
}
