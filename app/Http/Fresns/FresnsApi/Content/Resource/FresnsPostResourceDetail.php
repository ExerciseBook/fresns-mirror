<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Content\Resource;

use App\Base\Resources\BaseAdminResource;
use App\Http\Fresns\FresnsGroups\FresnsGroupsConfig;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\Fresns\FresnsPostAppends\FresnsPostAppends;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsMemberIcons\FresnsMemberIcons;
use App\Http\Fresns\FresnsComments\FresnsComments;
use App\Http\Fresns\FresnsGroups\FresnsGroups;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\Fresns\FresnsApi\Info\AmService;
use App\Http\Fresns\FresnsPlugin\FresnsPlugin;
use App\Http\Fresns\FresnsApi\Content\AmConfig;
use App\Http\Fresns\FresnsFiles\FresnsFiles;
use App\Http\Fresns\FresnsPosts\FresnsPostsConfig;
use App\Http\Fresns\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use Illuminate\Support\Facades\DB;
use App\Http\Fresns\FresnsPostAllows\FresnsPostAllowsConfig;
use App\Http\Fresns\FresnsComments\FresnsCommentsConfig;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShields;
use App\Http\Fresns\FresnsPostAppends\FresnsPostAppendsConfig;
use App\Http\Fresns\FresnsMemberIcons\FresnsMemberIconsConfig;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRolesConfig;
use App\Http\Fresns\FresnsExtends\FresnsExtends;
use App\Http\Fresns\FresnsExtendLinkeds\FresnsExtendLinkeds;
use App\Http\Fresns\FresnsImplants\FresnsImplants;
use App\Http\Fresns\FresnsImplants\FresnsImplantsConfig;
use App\Http\Fresns\FresnsMembers\FresnsMembersConfig;
use App\Http\Fresns\FresnsExtends\FresnsExtendsConfig;
use App\Http\Fresns\FresnsPosts\FresnsPostsService;
use App\Helpers\DateHelper;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsagesService;
use App\Http\Share\AmGlobal\GlobalService;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\Fresns\FresnsDomainLinks\FresnsDomainLinksConfig;

class FresnsPostResourceDetail extends BaseAdminResource
{
    public function toArray($request)
    {
        // dd(Carbon::today()->toDateTimeString());
        // $dt = Carbon::createFromDate(2011, 8, 1);
        // form 字段
        // 副表
        // $append = FresnsPostAppends::findAppend('post_id',$this->id);
        $append = DB::table(FresnsPostAppendsConfig::CFG_TABLE)->where('post_id', $this->id)->first();
        if ($append) {
            $append = get_object_vars($append);
        }
        // dd($append);
        // dd($append);
        // 成员表
        $memberInfo = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $this->member_id)->first();
        // 成员角色关联表表
        $roleRels = FresnsMemberRoleRels::where('member_id', $this->member_id)->where('type', 2)->first();
        // 成员角色表
        $memberRole = [];
        if (!empty($roleRels)) {
            $memberRole = FresnsMemberRoles::find($roleRels['role_id']);
        }
        // dd($this->id);
        // $memberRole = TweetMemberRoles::find($roleRels['role_id']);
        // dd($memberRole);
        // 成员图标
        $memberIcon = FresnsMemberIcons::where('member_id', $this->member_id)->first();
        // dd(1);
        // 评论
        // 如果满足条件需要输出一条评论内容，如果点赞最多的评论作者已经删除（members > deleted_at），则顺延下一条评论输出
        $comments = DB::table('comments as c')->select('c.*')
            ->leftJoin("members as m", 'c.member_id', '=', 'm.id')
            ->where('c.post_id', $this->id)
            ->where('m.deleted_at', null)
            ->where('c.deleted_at', null)
            ->orderby('like_count', 'Desc')
            ->first();
        // $comments = FresnsComments::where('post_id',$this->id)->where('parent_id',0)->orderby('like_count','Desc')->first();
        // 小组
        $groupInfo = FresnsGroups::find($this->group_id);

        $pid = $this->uuid;
        $mid = GlobalService::getGlobalKey('member_id');
        $input = [
            'member_id' => $mid,
            'like_type' => 4,
            'like_id' => $this->id,
        ];
        // $count = FresnsMemberLikes::where($input)->count();
        $count = DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where($input)->count();
        $isLike = $count == 0 ? false : true;
        $title = $this->title;
        // dump($this->content);
        $content = FresnsPostResource::getContentView(($this->content), ($this->id), 1);
        // dd($content);
        // dd($content);
        // 是否需要阅读权限
        $allowStatus = $this->is_allow;
        $allowProportion = 50;
        $noAllow = 0;
        if ($allowStatus == 1) {
            $memberCount = DB::table(FresnsPostAllowsConfig::CFG_TABLE)->where('post_id', $this->id)->where('type',
                1)->where('object_id', $mid)->count();
            $memberoleCount = 0;
            if (!empty($roleRels)) {
                $memberoleCount = DB::table(FresnsPostAllowsConfig::CFG_TABLE)->where('post_id',
                    $this->id)->where('type', 2)->where('object_id', $roleRels['role_id'])->count();
            }
            // 有阅读权限    
            if ($memberCount > 0 || $memberoleCount > 0) {
                $allowStatus = 1;
                $allowProportion = 100;
                $noAllow = 1;
            } else {
                $allowProportion = $append['allow_proportion'];
                if (!$allowProportion) {
                    $allowProportion = ApiConfigHelper::getConfigByItemKey(AmConfig::SNS_PROPORTION);
                }
                // dd($allowProportion);
                // $contentLength = mb_strlen($this->content);
                // $length = ceil($contentLength * $allowProportion /100);
                // dd($length);
                $FresnsPostsService = new FresnsPostsService();
                // dd($contentLength);
                // 防止@ 链接等信息被截断
                $contentInfo = $FresnsPostsService->truncatedContentInfo($append['content']);
                // dd($content);
                // $content = $contentInfo['truncated_content'];
                $content = FresnsPostResource::getContentView(($this->content), ($this->id), 1);
                // dd($allowProportion);
                // $content = mb_substr($content,0,$length);
                $allowStatus = 0;

            }
        } else {
            $noAllow = 1;
        }
        $brief = $this->is_brief;
        $sticky = $this->sticky_status;
        $essence = $this->essence_status;
        $more_json_decode = json_decode($this->more_json, true);
        $labelImg = $more_json_decode['labelImg'] ?? "";
        $titleIcon = $more_json_decode['titleIcon'] ?? "";
        $likeIcon = $more_json_decode['likeIcon'] ?? "";
        $followIcon = $more_json_decode['followIcon'] ?? "";
        $commentIcon = $more_json_decode['commentIcon'] ?? "";
        $shareIcon = $more_json_decode['shareIcon'] ?? "";
        $moreIcon = $more_json_decode['moreIcon'] ?? "";
        // 是否关注
        // $followStatus = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',4)->where('follow_id',$this->id)->count();
        $followStatus = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type',
            4)->where('follow_id', $this->id)->count();
        // 是否点赞
        // $likeStatus = FresnsMemberLikes::where('member_id',$mid)->where('like_type',4)->where('like_id',$this->id)->count();
        $likeStatus = DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('member_id', $mid)->where('like_type',
            4)->where('like_id', $this->id)->count();
        // 是否屏蔽
        // $shieldStatus = FresnsMemberShields::where('member_id',$mid)->where('shield_type',4)->where('shield_id',$this->id)->count();
        $shieldStatus = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id', $mid)->where('shield_type',
            4)->where('shield_id', $this->id)->count();
        $shieldSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::SHIELD_SETTING);
        $likeSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::LIKE_GROUP_SETTING);
        $followSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::GROUP_FOLLOW);
        $PostName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::POST_NAME) ?? "帖子";
        $followName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::POST_FOLLOW_NAME) ?? "加入";
        $likeName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::POST_LIKE_NAME) ?? "点赞";
        $shieldName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value',
                AmConfig::POST_SHIELD_NAME) ?? "屏蔽";
        $viewCount = $this->view_count;
        $likeCount = $this->like_count;
        $followCount = $this->follow_count;
        $shieldCount = $this->shield_count;
        $commentCount = $this->comment_count;
        $commentLikeCount = $this->comment_like_count;
        $time = $this->created_at;
        $timeFormat = DateHelper::format_date(strtotime($time));
        $timeFormat = str_replace("前", 'ago', $timeFormat);
        $editTime = $this->latest_edit_at == null ? $this->updated_at : $this->latest_edit_at;
        // dd($editTime);
        $editTimeFormat = DateHelper::format_date(strtotime($editTime));
        $editTimeFormat = str_replace("前", 'ago', $editTimeFormat);
        $canDelete = $append['can_delete'];
        $allowStatus = $this->is_allow;
        // 多语言 副表allow_btn_name
        $allowBtnName = ApiLanguageHelper::getLanguages(FresnsPostsConfig::CFG_TABLE, 'allow_btn_name', $this->id);
        $allowBtnName = $allowBtnName == null ? "" : $allowBtnName['lang_content'];
        $allowBtnUrl = $append['allow_plugin_unikey'];
        // 多语言 member_list_name
        $memberListName = ApiLanguageHelper::getLanguages(FresnsPostsConfig::CFG_TABLE, 'member_list_name', $this->id);
        $memberListName = $memberListName == null ? "" : $memberListName['lang_content'];

        // memberListCount
        $memberListCount = Db::table('post_members')->where('post_id', $this->id)->count();
        $member = [];
        // dd($memberInfo);
        $member['anonymous'] = $this->is_anonymous;
        $member['deactivate'] = false;
        // $member['isAuthor'] = "";
        $member['mid'] = "";
        $member['mname'] = "";
        $member['nickname'] = "";
        $member['nicknameColor'] = "";
        $member['roleName'] = "";
        $member['roleIcon'] = "";
        $member['avatar'] = $memberInfo->avatar_file_url ?? "";
        // 为空用默认头像
        if (empty($member['avatar_file_url'])) {
            $defaultIcon = ApiConfigHelper::getConfigByItemKey(AmConfig::DEFAULT_AVATAR);
            $member['avatar'] = $defaultIcon;
        }
        // 匿名头像 anonymous_avatar 键值
        if ($this->is_anonymous == 1) {
            $anonymousAvatar = ApiConfigHelper::getConfigByItemKey(AmConfig::ANONYMOUS_AVATAR);
            $member['avatar'] = $anonymousAvatar;

        }
        // 已注销头像 deactivate_avatar 键值"
        if ($memberInfo) {
            if ($memberInfo->deleted_at != null) {
                $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(AmConfig::DEACTIVATE_AVATAR);
                $member['avatar'] = $deactivateAvatar;
            }
        } else {
            $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(AmConfig::DEACTIVATE_AVATAR);
            $member['avatar'] = $deactivateAvatar;
        }
        $member['avatar'] = ApiFileHelper::getImageSignUrl($member['avatar']);
        $member['decorate'] = "";
        $member['gender'] = "";
        $member['bio'] = "";
        $member['verifiedStatus'] = "";
        $member['verifiedIcon'] = "";
        $icons = [];
        $icons['icon'] = "";
        $icons['name'] = "";
        $member['icons'] = $icons;
        // dd($member['avatar']);
        if ($this->is_anonymous == 0) {
            if ($memberInfo->deleted_at == null && $memberInfo) {
                $member['anonymous'] = $this->is_anonymous;
                $member['deactivate'] = true;
                // $member['isAuthor'] = $this->member_id == $mid ? true :false;
                $member['mid'] = $memberInfo->uuid ?? "";
                $member['mname'] = $memberInfo->name ?? "";
                $member['nickname'] = $memberInfo->nickname ?? "";
                $member['nicknameColor'] = $memberRole['nickname_color'] ?? "";
                // "roleName": "主角色的值，如果为不显示则不输出 member_roles > name 多语言",
                $roleName = "";
                if (!empty($memberRole)) {
                    $roleName = ApiLanguageHelper::getLanguages(FresnsMemberRolesConfig::CFG_TABLE, 'name',
                        $memberRole['id']);
                    $roleName = $roleName == null ? "" : $roleName['lang_content'];
                }

                $member['roleName'] = $roleName;
                $member['roleIcon'] = $memberRole['icon_file_url'] ?? "";
                // $member['avatar'] = $member['avatar'];

                // $member['decorate'] = $memberInfo->decorate_file_url ?? "";
                $member['decorate'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberInfo->decorate_file_id,
                    $memberInfo->decorate_file_url);
                $member['gender'] = $memberInfo->gender ?? "";
                $member['bio'] = $memberInfo->bio ?? "";
                $member['verifiedStatus'] = $memberInfo->verified_status ?? "";
                // $member['verifiedIcon'] = $memberInfo->verified_file_url ?? "";
                $member['verifiedIcon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberInfo->verified_file_id,
                    $memberInfo->verified_file_url);
                $icons = [];
                $icons['icon'] = $memberIcon['icon_file_url'] ?? "";
                if($icons['icon']){
                    $icons['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberIcon['icon_file_id'],$memberIcon['icon_file_url']);
                }
                // 多语言 icon  name
                $icons['name'] = "";
                if ($memberIcon) {
                    $iconName = ApiLanguageHelper::getLanguages(FresnsMemberIconsConfig::CFG_TABLE, 'name',
                        $memberIcon['id']);
                    $iconName = $iconName == null ? "" : $iconName['lang_content'];
                    $icons['name'] = $iconName;
                }

                $member['icons'] = $icons;
            }
        }
        $postHotStatus = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_HOT);
        $postHotStatus = $postHotStatus == null ? 0 : $postHotStatus;
        $comment = [];
        // dump($comments);
        // if($postHotStatus != 0 && !empty($comments)){
        //     // 查询评论人员信息
        //     // if(!empty($comments)){
        //         $commentMemberInfo = FresnsMembers::find($comments['member_id']);
        //         $comment['status'] = $postHotStatus;
        //         $comment['anonymous'] = $comments['is_anonymous'] ?? "";
        //         // 该条评论的作者是不是帖子作者自己
        //         $commentStatus = $this->member_id == $comments['member_id'] ? true :false;
        //         if($comments['is_anonymous'] && $comments['is_anonymous'] == 0){
        //             $comment['isAuthor'] = $commentStatus;
        //             $comment['mid'] = $commentMemberInfo['id'] ?? "";
        //             $comment['mname'] = $commentMemberInfo['name'] ?? "";
        //             $comment['nickname'] =  $commentMemberInfo['nickname'] ?? "";
        //         }
        //         $comment['cid'] = $comments['uuid'] ?? "";
        //         // $comment['content'] = $comments['content'];
        //         $comment['content'] = FresnsPostResource::getContentView($comments['content']);
        //         $comment['likeCount'] = $comments['like_count'] ?? "";
        //         $images = [];
        //         $comment['images'] = $images;
        //         $fileInfo = FresnsFiles::where('file_type',2)->where('table_name',FresnsCommentsConfig::CFG_TABLE)->where('table_field','id')->where('table_id',$comments['id'])->first();
        //         if($fileInfo){
        //             $comment['imageCount'] = 1;
        //             $images = ApiFileHelper::antiTheftFile($fileInfo['id']);
        //         }else{
        //             $comment['imageCount'] = 0;
        //         }
        //     // }
        // }
        $location = [];
        $location['isLbs'] = $this->is_lbs;
        $location['mapId'] = $this->map_id;
        $location['latitude'] = $this->map_latitude;
        $location['longitude'] = $this->map_longitude;
        $location['scale'] = $append['map_scale'];
        $location['poi'] = $append['map_poi'];
        $location['poiId'] = $append['map_poi_id'];
        $location['distance'] = "";
        $longitude = request()->input('longitude', "");
        $latitude = request()->input('latitude', "");
        $langTag = request()->header('langTag', "");
        // dd(1);
        if ($longitude && $latitude && $this->map_latitude && $this->map_longitude) {
            // 获取单位
            $distanceUnits = request()->input('lengthUnits');
            if (!$distanceUnits) {
                // 距离
                $languages = ApiConfigHelper::distanceUnits($langTag);
                $distanceUnits = empty($languages) ? 'km' : $languages;
            }

            // dd($languageConfig);
            $location['distance'] = $this->GetDistance($latitude, $longitude, $this->map_latitude, $this->map_longitude,
                $distanceUnits);
        }
        $attachedQuantity = [];
        $attachedQuantity['image'] = FresnsFiles::where('file_type', 2)->where('table_name',
            FresnsPostsConfig::CFG_TABLE)->where('table_id', $this->id)->count();
        $attachedQuantity['videos'] = FresnsFiles::where('file_type', 3)->where('table_name',
            FresnsPostsConfig::CFG_TABLE)->where('table_id', $this->id)->count();
        $attachedQuantity['audios'] = FresnsFiles::where('file_type', 4)->where('table_name',
            FresnsPostsConfig::CFG_TABLE)->where('table_id', $this->id)->count();
        $attachedQuantity['docs'] = FresnsFiles::where('file_type', 5)->where('table_name',
            FresnsPostsConfig::CFG_TABLE)->where('table_id', $this->id)->count();
        $attachedQuantity['extends'] = Db::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('linked_type',
            1)->where('linked_id', $this->id)->count();

        $files = [];
        $extends = [];
        // dump($noAllow);
        $extendsLinks = Db::table('extend_linkeds')->where('linked_type', 1)->where('linked_id', $this->id)->first();
        $extendsLinks = [];
        if ($extendsLinks) {
            $extendsLinks = array_unique($extendsLinks);
            $extendsInfo = FresnsExtends::whereIn('id', $extendsLinks)->get();
        }
        // dd($this->id);
        // $files = ApiFileHelper::getFileInfoByTable(FresnsPostsConfig::CFG_TABLE,$this->id);
        // dd($files);
        // $more_json = json_decode($this->more_json,true);
        // if($more_json){
        //     $files = $more_json['files'];
        // }
        if ($noAllow != 0) {
            $more_json = json_decode($this->more_json, true);
            if ($more_json) {
                // $files = $more_json['files'];
                $files = ApiFileHelper::getMoreJsonSignUrl($more_json['files']);
            }
            // $extends = $more_json_decode['extends'];
            // extends 
            // $extendsInfo = FresnsExtends::where('post_id',$this->id)->first();
            if (!empty($extendsInfo)) {
                $extends = [];
                foreach ($extendsInfo as $e) {
                    $arr = [];
                    $arr['eid'] = $e['uuid'] ?? "";
                    $arr['plugin'] = $e['plugin_unikey'] ?? "";
                    $arr['frame'] = $e['frame'] ?? "";
                    $arr['position'] = $e['position'] ?? "";
                    $arr['content'] = $e['text_content'] ?? "";
                    // $arr['files'] = ApiFileHelper::getFileInfoByTable(FresnsPostsConfig::CFG_TABLE,$this->id);
                    if ($arr['frame'] == 1) {
                        $arr['files'] = $e['text_files'];
                    }
                    $arr['cover'] = $e['cover_file_url'] ?? "";
                    if($arr['cover']){
                        $arr['cover'] =  ApiFileHelper::getImageSignUrlByFileIdUrl($e['cover_file_id'], $e['cover_file_url']);
                    }
                    $arr['title'] = "";
                    if (!empty($e)) {
                        $title = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'title', $e['id']);
                        $title = $title == null ? "" : $title['lang_content'];
                        $arr['title'] = $title;
                    }

                    $arr['titleColor'] = $e['title_color'] ?? "";
                    $arr['descPrimary'] = "";
                    if (!empty($e)) {
                        $descPrimary = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'desc_primary',
                            $e['id']);
                        $descPrimary = $descPrimary == null ? "" : $descPrimary['lang_content'];
                        $arr['descPrimary'] = $descPrimary;
                    }
                    $arr['descPrimaryColor'] = $e['desc_primary_color'] ?? "";
                    $arr['descSecondary'] = "";
                    if (!empty($e)) {
                        $descSecondary = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE,
                            'desc_secondary', $e['id']);
                        $descSecondary = $descSecondary == null ? "" : $descSecondary['lang_content'];
                        $arr['descSecondary'] = $descSecondary;
                    }

                    $arr['descSecondaryColor'] = $e['desc_secondary_color'] ?? "";
                    $arr['descPrimaryColor'] = $e['desc_primary_color'] ?? "";
                    $arr['btnName'] = "";
                    if (!empty($e)) {
                        $btnName = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'btn_name',
                            $e['id']);
                        $btnName = $btnName == null ? "" : $btnName['lang_content'];
                        $arr['btnName'] = $btnName;
                    }
                    $arr['btnColor'] = $e['btn_color'] ?? "";
                    $arr['type'] = $e['extend_type'] ?? "";
                    $arr['target'] = $e['extend_target'] ?? "";
                    $arr['value'] = $e['extend_value'] ?? "";
                    $arr['support'] = $e['extend_support'] ?? "";
                    $arr['moreJson'] = ApiFileHelper::getMoreJsonSignUrl($e['moreJson'] ) ?? "";
                    $extends[] = $arr;
                }
            }
        }
        $group = [];
        if ($groupInfo) {
            $group['gid'] = $groupInfo['uuid'] ?? "";
            $name = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'name', $this->group_id);
            $group['gname'] = $name == null ? "" : $name['lang_content'];
            $description = ApiLanguageHelper::getLanguages(FresnsGroupsConfig::CFG_TABLE, 'description',
                $this->group_id);
            $group['description'] = $description == null ? "" : $description['lang_content'];
            $group['cover'] = $groupInfo['cover_file_url'] ?? "";
            // 当前请求接口的成员，是否拥有该小组评论权限
            $permission = $groupInfo['permission'] ?? "";
            $permissionArr = json_decode($permission, true);
            $group['allow'] = true;
            if ($permissionArr) {
                $publish_comment = $permissionArr['publish_comment'];
                $publish_post = $permissionArr['publish_post'];
                $publish_comment_roles = $permissionArr['publish_comment_roles'];
                $group['allow'] = false;
                // 1.所有人
                if ($publish_comment == 1) {
                    $group['allow'] = true;
                }
                //  2.仅关注了小组的成员
                if ($publish_comment == 2) {
                    // $followCount = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',2)->where('follow_id',$this->group_id)->count();
                    $followCount = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',
                        $mid)->where('follow_type', 2)->where('follow_id', $this->group_id)->count();
                    if ($followCount > 0) {
                        $group['allow'] = true;
                    }
                }
                // 3.仅指定的角色成员
                if ($publish_post == 3) {
                    $memberRoleArr = FresnsMemberRoleRels::where('member_id', $mid)->pluck('role_id')->toArray();
                    $arrIntersect = array_intersect($memberRoleArr, $publish_comment_roles);
                    if ($arrIntersect) {
                        $group['allow'] = true;
                    }
                }
            }
            $group['viewCount'] = $groupInfo['view_count'] ?? "";
            $group['likeCount'] = $groupInfo['like_count'] ?? "";
            $group['followCount'] = $groupInfo['follow_count'] ?? "";
            $group['shieldCount'] = $groupInfo['shield_count'] ?? "";
            $group['postCount'] = $groupInfo['post_count'] ?? "";
            $group['essenceCount'] = $groupInfo['essence_count'] ?? "";
        }
        $manages = [];
        #
        /**1、当 plugin_usages > scene 应用场景不包含「帖子」的插件，不输出。
         * 2、当 plugin_usages > is_group_admin 为小组管理员专用，则判断接口请求的成员是否为管理员。
         * 2.1、当 posts > group_id 为空时，代表帖子无小组，小组管理员专用插件无效不输出。
         * 2.2、根据 posts > group_id 和 groups > admin_members 查询该字段中是否含有该成员的 mid，无则不输出。
         */
        #
        $TweetPluginUsages = FresnsPluginUsages::where('type', 5)->where('scene', 'like', "%1%")->first();
        // dd($TweetPluginUsages['plugin_unikey']);
        if ($TweetPluginUsages) {
            $manages['plugin'] = $TweetPluginUsages['plugin_unikey'];
            $plugin = FresnsPlugin::where('unikey', $TweetPluginUsages['plugin_unikey'])->first();
            // dd($plugin);
            $name = AmService::getlanguageField('name', $TweetPluginUsages['id']);
            $manages['name'] = $name == null ? "" : $name['lang_content'];
            $manages['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($TweetPluginUsages['icon_file_id'],$TweetPluginUsages['icon_file_url']);
            $manages['url'] = ApiFileHelper::getPluginUsagesUrl($TweetPluginUsages['plugin_unikey'],$TweetPluginUsages['id']);

            // 是否管理员专用
            if ($TweetPluginUsages['is_group_admin'] != 0) {
                // 查询登录用户是否为管理员
                // $roleRels = FresnsMemberRoleRels::where('member_id',$mid)->where('type',2)->pluck('role_id')->toArray();
                // $roles = FresnsMemberRoles::whereIn('id',$roleRels)->where('type',1)->count();
                if (!$this->group_id) {
                    $manages = [];
                } else {
                    $groupInfo = FresnsGroups::find($this->group_id);
                    if (!$groupInfo) {
                        $manages = [];
                    } else {
                        $permission = json_decode($groupInfo['permission'], true);
                        // dump($permission);
                        // dump($mid);
                        if (isset($permission['admin_members'])) {
                            if (!is_array($permission['admin_members'])) {
                                $manages = [];
                            } else {
                                if (!in_array($mid, $permission['admin_members'])) {
                                    $manages = [];
                                }
                            }
                        } else {
                            $manages = [];
                        }
                    }
                }
            }
            // dd($manages);
            // plugin_usages > member_roles 有值，则判断当前请求成员的所有角色 ID 是否包含在其中，在则输出，不在不输出（未登录则不在）。
            if ($TweetPluginUsages['member_roles']) {
                $mroleRels = FresnsMemberRoleRels::where('member_id', $mid)->first();
                // dd( $mroleRels['role_id']);
                if ($mroleRels) {
                    $pluMemberRoleArr = explode(',', $TweetPluginUsages['member_roles']);
                    // dump($mroleRels);
                    // dd($pluMemberRoleArr);
                    if (!in_array($mroleRels['role_id'], $pluMemberRoleArr)) {
                        $manages = [];
                    }
                }
            }
        }
        // editStatus
        $editStatus = [];
        // 该篇帖子作者是否为本人
        $editStatus['isMe'] = $this->member_id == $mid ? true : false;
        // 帖子编辑权限
        $postEdit = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_EDIT) ?? false;
        $editTimes = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_EDIT_TIMELIMIT) ?? 5;
        $editSticky = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_EDIT_STICKY) ?? false;
        $editEssence = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_EDIT_ESSENCE) ?? false;
        if ($postEdit) {
            // 多长时间内可以编辑
            if (strtotime($this->created_at) + ($editTimes * 60) > time()) {
                $postEdit = false;
            }
            // 帖子置顶后编辑权限
            if ($this->sticky_status != 0) {
                if (!$editSticky) {
                    $postEdit = false;
                }
            }

            // 帖子加精后编辑权限
            if ($this->essence_status != 0) {
                if (!$editEssence) {
                    $postEdit = false;
                }
            }

        }
        $editStatus['canEdit'] = $postEdit;
        // dd($postEdit);
        $editStatus['canDelete'] = $append['can_delete'] == 1 ? true : false;
        if (!$langTag) {
            $langTag = FresnsPluginUsagesService::getDefaultLanguage();
        }
        $seo = DB::table('seo')->where('linked_type', 4)->where('linked_id', $this->id)->where('lang_tag',
            $langTag)->where('deleted_at', null)->first();
        $seoInfo = [];
        if ($seo) {
            $seoInfo['title'] = $seo->title;
            $seoInfo['keywords'] = $seo->keywords;
            $seoInfo['description'] = $seo->description;
        }
        $more_json = json_decode($this->more_json, true);
        $icons = $more_json['icons'] ?? [];
        // 默认字段
        $default = [
            'pid' => $pid,
            // 'titleIcon' => $titleIcon,
            // 'isLike' => $isLike,
            'title' => $title,
            'content' => $content,
            'isMarkdown' => $append['is_markdown'],
            // 'brief' => $brief,
            'sticky' => $sticky,
            'essence' => $essence,
            // 'labelImg' => $labelImg,
            // 'likeIcon' => $likeIcon,
            // 'followIcon' => $followIcon,
            // 'shareIcon' => $shareIcon,
            // 'commentIcon' => $commentIcon,
            // 'moreIcon' => $moreIcon,
            'PostName' => $PostName,
            'likeSetting' => $likeSetting,
            'likeName' => $likeName,
            'likeStatus' => $likeStatus,
            'followSetting' => $followSetting,
            'followName' => $followName,
            'followStatus' => $followStatus,
            'shieldSetting' => $shieldSetting,
            'shieldName' => $shieldName,
            'shieldStatus' => $shieldStatus,
            'memberListStatus' => $append['member_list_status'],
            'memberListName' => $memberListName,
            'memberListCount' => $memberListCount,
            'memberListUrl' => $append['member_list_plugin_unikey'],
            'viewCount' => $viewCount,
            'likeCount' => $likeCount,
            'followCount' => $followCount,
            'shieldCount' => $shieldCount,
            'commentCount' => $commentCount,
            'commentLikeCount' => $commentLikeCount,
            'time' => $time,
            'timeFormat' => $timeFormat,
            'editTime' => $editTime,
            'editTimeFormat' => $editTimeFormat,
            'editCount' => $append['edit_count'],
            // 'canDelete' => $canDelete,
            'allowStatus' => $allowStatus,
            'allowProportion' => $allowProportion,
            'allowBtnName' => $allowBtnName,
            'allowBtnUrl' => $allowBtnUrl,
            'member' => $member,
            'icons' => $icons,
            // 'commentSetting' => $comment,
            'location' => $location,
            'attachedQuantity' => $attachedQuantity,
            'files' => $files,
            'extends' => $extends,
            'group' => $group,
            'manages' => $manages,
            'editStatus' => $editStatus,
            'seoInfo' => $seoInfo

        ];
        // 合并
        $arr = $default;

        return $arr;
    }

    public function GetDistance($lat1, $lng1, $lat2, $lng2, $distanceUnits)
    {

        $EARTH_RADIUS = 6378.137;

        $radLat1 = $this->rad($lat1);
        $radLat2 = $this->rad($lat2);
        $a = $radLat1 - $radLat2;
        $b = $this->rad($lng1) - $this->rad($lng2);
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * $EARTH_RADIUS;
        // $unitCounts = 
        // 1 千米(km)=0.621371192237 英里(mi)
        if ($distanceUnits == 'mi') {
            $s = round($s * 10000 * 0.62);
        } else {
            $s = round($s * 10000);
        }
        $s = round($s / 10000) == 0 ? 1 : round($s / 10000);
        return $s.$distanceUnits;
    }

    private function rad($d)
    {
        return $d * M_PI / 180.0;
    }
}

