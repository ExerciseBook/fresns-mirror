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
use App\Http\Fresns\FresnsApi\Info\AmService;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShields;
use App\Http\Fresns\FresnsGroups\FresnsGroups;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsGroups\FresnsGroupsService;
use App\Http\Share\AmGlobal\GlobalService;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShieldsConfig;
use Illuminate\Support\Facades\DB;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;

class FresnsGroupTreeResource extends BaseAdminResource
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
        $mid = GlobalService::getGlobalKey('member_id');
        // dd($mid);
        $groupSize = $request->input('groupSize');
        $gid = $this->uuid;
        $type = $this->type;
        $parentId = $this->parent_id;
        // 语言
        $name = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->id);
        $description = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'description', $this->id);
        $gname = $name == null ? "" : $name['lang_content'];
        $description = $description == null ? "" : $description['lang_content'];
        // $cover = $this->cover_file_url;
        $cover = ApiFileHelper::getImageSignUrlByFileIdUrl($this->cover_file_id, $this->cover_file_url);
        // $banner = $this->banner_file_url;
        $banner = ApiFileHelper::getImageSignUrlByFileIdUrl($this->banner_file_id, $this->banner_file_url);
        // $groupCount = FresnsGroups::where('parent_id',$this->id)->count();
        $groups = [];
        // dump($mid);
        // 如果是非公开小组的帖子，不是小组成员，不输出。
        $FresnsGroups = FresnsGroups::where('type_mode', 2)->where('type_find', 2)->pluck('id')->toArray();
        // $groupMember = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->pluck('follow_id')->toArray();
        $groupMember = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type',
            2)->pluck('follow_id')->toArray();
        $noGroupArr = array_diff($FresnsGroups, $groupMember);
        $cGroups = FresnsGroups::where('parent_id', $this->id)->whereNotIn('id',
            $noGroupArr)->limit($groupSize)->orderby('rank_num', 'asc')->get()->toArray();
        $groupCount = count($cGroups);

        // $groupArr = FresnsGroups::whereNotIn('id',$noGroupArr)->pluck('id')->toArray();
        // if($mid){
        //     // 不需要关注获取的小组
        //     $noFollowGroupIdArr1 = FresnsGroups::where('type_find',2)->where('type_mode',2)->pluck('id')->toArray();
        //     $noFollowGroupIdArr1 = FresnsGroups::whereNotIn('id',$noFollowGroupIdArr1)->pluck('id')->toArray();
        //     // $noFollowGroupIdArr2 = FresnsGroups::where('type_mode',2)->where('parent_id',null)->where('type_find',1)->pluck('id')->toArray();
        //     // 查询需要关注才能获取的小组
        //     $groupIdArr = FresnsGroups::where('type_mode',2)->where('type_find',2)->pluck('id')->toArray();
        //     $memberGroupArr = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->whereIn('follow_id',$groupIdArr)->pluck('follow_id')->toArray();
        //     // dd($memberGroupArr);
        //     $noFollowGroupIdArr = array_merge($noFollowGroupIdArr1,$memberGroupArr);
        //     $cGroups = FresnsGroups::where('parent_id',$this->id)->whereIn('id',$noFollowGroupIdArr)->limit($groupSize)->orderby('rank_num','asc')->get()->toArray();
        // }else{
        //     // 查询type_find 模式为“不可发现”
        //     $groupArr = FresnsGroups::where('type_find',2)->where('type_mode',2)->pluck('id')->toArray();
        //     $cGroups = FresnsGroups::where('parent_id',$this->id)->whereNotIn('id',$groupArr)->limit($groupSize)->orderby('rank_num','asc')->get()->toArray();
        // }
        // $cGroups = FresnsGroups::where('parent_id',$this->id)->limit($groupSize)->get()->toArray();
        if ($cGroups) {
            $arr = [];
            foreach ($cGroups as $c) {
                // dump($c);
                $arr['gid'] = $c['uuid'];
                // 语言
                $cname = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $c['id']);
                $cdescription = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'description', $c['id']);
                $arr['gname'] = $cname == null ? "" : $cname['lang_content'];
                $arr['type'] = $c['type'];
                $arr['description'] = $cdescription == null ? "" : $cdescription['lang_content'];
                // $arr['cover'] = $c['cover_file_url'];
                // $arr['banner'] = $c['banner_file_url'];
                // $cover = $this->cover_file_url;
                $arr['cover'] = ApiFileHelper::getImageSignUrlByFileIdUrl($c['cover_file_id'], $c['cover_file_url']);
                // $banner = $this->banner_file_url;
                $arr['banner'] = ApiFileHelper::getImageSignUrlByFileIdUrl($c['banner_file_id'], $c['banner_file_url']);
                $arr['recommend'] = $c['is_recommend'];
                $arr['groupName'] = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE,
                        'item_value', AmConfig::GROUP_NAME) ?? "小组";
                $arr['followSetting'] = ApiConfigHelper::getConfigByItemKey(AmConfig::FOLLOW_GROUP_SETTING);
                $arr['followName'] = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE,
                        'item_value', AmConfig::GROUP_FOLLOW_NAME) ?? "加入";
                // 是否关注
                // $arr['followStatus'] = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->where('follow_id',$c['id'])->count();
                $arr['followStatus'] = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',
                    $mid)->where('follow_type', 2)->where('follow_id', $c['id'])->count();
                // dump($arr['followStatus']);
                $arr['followType'] = $c['type_follow'];
                $arr['followUrl'] = $c['plugin_unikey'];
                $arr['likeSetting'] = ApiConfigHelper::getConfigByItemKey(AmConfig::LIKE_GROUP_SETTING);
                $arr['likeName'] = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE,
                        'item_value', AmConfig::GROUP_LIKE_NAME) ?? "点赞";
                // $arr['likeStatus'] = FresnsMemberLikes::where('member_id',$mid)->where('like_type',2)->where('like_id',$c['id'])->count();
                $arr['likeStatus'] = DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('member_id',
                    $mid)->where('like_type', 2)->where('like_id', $c['id'])->count();
                $arr['shieldSetting'] = ApiConfigHelper::getConfigByItemKey(AmConfig::SHIELD_SETTING);
                $arr['shieldName'] = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE,
                        'item_value', AmConfig::GROUP_SHIELD_NAME) ?? "屏蔽";
                // $arr['shieldStatus'] = FresnsMemberShields::where('member_id',$mid)->where('shield_type',2)->where('shield_id',$this->id)->count();
                $arr['shieldStatus'] = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id',
                    $mid)->where('shield_type', 2)->where('shield_id', $c['id'])->count();
                $arr['viewCount'] = $c['view_count'];
                $arr['likeCount'] = $c['like_count'];
                $arr['followCount'] = $c['follow_count'];
                $arr['shieldCount'] = $c['shield_count'];
                $arr['postCount'] = $c['post_count'];
                $arr['essenceCount'] = $c['essence_count'];
                // 小组管理员列表
                // dump($c['permission']);
                $arr['adminMemberArr'] = FresnsGroupsService::adminData($c['permission']);
                // $permissionArr = json_decode($c['permission'],true);
                // // dd($permissionArr);
                // $admin_member = $permissionArr['admin_members'];
                // $publish_post = $permissionArr['publish_post'];
                // $publish_post_roles = $permissionArr['publish_post_roles'];
                // $publish_post_review = $permissionArr['publish_post_review'];
                // $publish_comment = $permissionArr['publish_comment'];
                // $publish_comment_roles = $permissionArr['publish_comment_roles'];
                // $publish_comment_review = $permissionArr['publish_comment_review'];
                // // dd($permissionArr);
                // $adminMemberArr = [];
                // if($admin_member){
                //     foreach($admin_member as $a){
                //        $array = [];
                //        $memberInfo = TweetMembers::find($a);
                //        if($memberInfo){
                //             $array['mid'] = $memberInfo['uuid'];
                //             $array['mname'] = $memberInfo['name'];
                //             $array['nickname'] = $memberInfo['nickname'];
                //             $array['nicknameColor'] = $memberInfo['uuid'];
                //             // 成员角色关联表表
                //             $roleRels = FresnsMemberRoleRels::where('member_id',$memberInfo['id'])->first();
                //             if(!empty($roleRels)){
                //                 $memberRole = TweetMemberRoles::find($roleRels['role_id']);
                //             }
                //             $array['nicknameColor'] = $memberRole['nickname_color'] ?? "";
                //             $array['avatar'] = $memberInfo['avatar_file_url'];
                //             $adminMemberArr[] = $array;
                //        }
                //     }
                // }
                // $arr['adminMemberArr'] = $adminMemberArr;

                // 当前请求接口的成员，是否拥有该小组发表帖子权限
                $arr['publishRule'] = FresnsGroupsService::publishRule($mid, $c['permission'], $this->id);

                // groups 表 permission 字段其他内容
                $arr['permission'] = FresnsGroupsService::othetPession($c['permission']);
                // $publishRule = [];
                // $publishRule['allowPost'] = false;
                // // 1.所有人
                // if($publish_post == 1){
                //     $publishRule['allowPost'] = true;
                // }
                // //  2.仅关注了小组的成员
                // if($publish_post == 2){
                //     $followCount = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->where('follow_id',$c['id'])->count();
                //     if($followCount > 0){
                //         $publishRule['allowPost'] = true;
                //     }
                // }
                // // 3.仅指定的角色成员
                // if($publish_post == 3){
                //    $memberRoleArr = FresnsMemberRoleRels::where('member_id',$mid)->pluck('role_id')->toArray();
                //    $arrIntersect = array_intersect($memberRoleArr,$publish_post_roles);
                //    if($arrIntersect){
                //         $publishRule['allowPost'] = true;
                //    }
                // }
                // // 当前请求接口的成员，发帖是否需要审核（如果是管理员，无需审核）
                // $publishRule['reviewPost'] = true;
                // if($publish_post_review == 0){
                //     $publishRule['reviewPost'] = false;
                // }
                // if(in_array($mid,$admin_member)){
                //     $publishRule['reviewPost'] = false;
                // }
                // $publishRule['allowComment'] = false;
                // // 1.所有人
                // if($publish_comment == 1){
                //     $publishRule['allowComment'] = true;
                // }
                // //  2.仅关注了小组的成员
                // if($publish_comment == 2){
                //     $followCount = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->where('follow_id',$c['id'])->count();
                //     if($followCount > 0){
                //         $publishRule['allowComment'] = true;
                //     }
                // }
                // // 3.仅指定的角色成员
                // if($publish_post == 3){
                //    $memberRoleArr = FresnsMemberRoleRels::where('member_id',$mid)->pluck('role_id')->toArray();
                //    $arrIntersect = array_intersect($memberRoleArr,$publish_comment_roles);
                //    if($arrIntersect){
                //         $publishRule['allowComment'] = true;
                //    }
                // }
                // $publishRule['reviewComment'] = true;
                // if($publish_comment_review == 0){
                //     $publishRule['reviewComment'] = false;
                // }
                // if(in_array($mid,$admin_member)){
                //     $publishRule['reviewComment'] = false;
                // }
                // $arr['publishRule'] = $publishRule;
                $groups[] = $arr;
            }
        }
        // 默认字段
        $default = [
            'gid' => $gid,
            'gname' => $gname,
            'description' => $description,
            'cover' => $cover,
            'banner' => $banner,
            'groupCount' => $groupCount,
            'groups' => $groups,
        ];
        // 合并
        $arr = $default;

        return $arr;
    }
}

