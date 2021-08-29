<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Content\Resource;

use App\Base\Resources\BaseAdminResource;
use App\Helpers\DateHelper;
use App\Http\Center\Common\GlobalService;
use App\Http\FresnsApi\Content\AmConfig;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsApi\Info\AmService;
use App\Http\FresnsDb\FresnsCommentAppends\FresnsCommentAppends;
use App\Http\FresnsDb\FresnsCommentAppends\FresnsCommentAppendsConfig;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsComments\FresnsCommentsConfig;
use App\Http\FresnsDb\FresnsComments\FresnsCommentsService;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsExtendLinkeds\FresnsExtendLinkeds;
use App\Http\FresnsDb\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsExtends\FresnsExtendsConfig;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsConfig;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\FresnsDb\FresnsMemberIcons\FresnsMemberIcons;
use App\Http\FresnsDb\FresnsMemberIcons\FresnsMemberIconsConfig;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRolesConfig;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShields;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\FresnsDb\FresnsPostAppends\FresnsPostAppends;
use App\Http\FresnsDb\FresnsPostAppends\FresnsPostAppendsConfig;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsConfig;
use Illuminate\Support\Facades\DB;

class CommentResource extends BaseAdminResource
{
    public function toArray($request)
    {

        // dd(1);
        // form 字段
        $formMap = FresnsCommentsConfig::FORM_FIELDS_MAP;
        $formMapFieldsArr = [];
        foreach ($formMap as $k => $dbField) {
            $formMapFieldsArr[$dbField] = $this->$dbField;
        }
        // 副表
        // $append = FresnsCommentAppends::findAppend('comment_id',$this->id);
        $append = DB::table(FresnsCommentAppendsConfig::CFG_TABLE)->where('comment_id', $this->id)->first();
        // if($append){
        //     $append =  get_object_vars($append);
        // }
        // 成员表
        $memberInfo = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $this->member_id)->first();
        // $memberInfo = TweetMembers::find($this->member_id);
        // 成员角色关联表表
        $roleRels = FresnsMemberRoleRels::where('member_id', $this->member_id)->where('type', 2)->first();
        // 成员角色表
        $memberRole = [];
        if (! empty($roleRels)) {
            $memberRole = FresnsMemberRoles::find($roleRels['role_id']);
        }
        // 成员图标
        $memberIcon = FresnsMemberIcons::where('member_id', $this->member_id)->first();
        // 帖子
        $posts = FresnsPosts::find($this->post_id);

        // 帖子副表
        // $postAppends = FresnsPostAppends::findAppend('post_id',$this->post_id);
        $postAppends = DB::table(FresnsPostAppendsConfig::CFG_TABLE)->where('post_id', $this->post_id)->first();
        if ($postAppends) {
            $postAppends = get_object_vars($postAppends);
        }
        // dd($postAppends);
        // 小组
        $groupInfo = FresnsGroups::find($posts['group_id']);
        //  $groupInfo = FresnsGroups::find($this->group_id);

        $cid = $this->uuid;
        $mid = GlobalService::getGlobalKey('member_id');

        $input = [
            'member_id' => $mid,
            'like_type' => 5,
            'like_id' => $this->id,
        ];
        // $count = FresnsMemberLikes::where($input)->count();
        $count = DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where($input)->count();
        $isLike = $count == 0 ? false : true;
        // 是否为屏蔽评论
        // $shieldsCount = FresnsMemberShields::where('member_id',$mid)->where('shield_type',5)->where('shield_id',$this->id)->count();
        $shieldsCount = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id', $mid)->where('shield_type', 5)->where('shield_id', $this->id)->count();
        $isShield = $shieldsCount == 0 ? false : true;
        $content = FresnsPostResource::getContentView($this->content, $this->id, 2);
        $brief = $this->is_brief;
        $sticky = $this->is_sticky;
        // $labelImg = $this->label_file_url;
        $likeCount = $this->like_count;
        $commentCount = $this->comment_count;
        $commentLikeCount = $this->comment_like_count;
        // 是否关注
        // $followStatus = FresnsMemberFollows::where('member_id',$mid)->where('follow_type',5)->where('follow_id',$this->id)->count();
        $followStatus = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type', 5)->where('follow_id', $this->id)->count();
        // 是否点赞
        // $likeStatus = FresnsMemberLikes::where('member_id',$mid)->where('like_type',5)->where('like_id',$this->id)->count();
        $likeStatus = DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('member_id', $mid)->where('like_type', 5)->where('like_id', $this->id)->count();
        // 是否屏蔽
        // $shieldStatus = FresnsMemberShields::where('member_id',$mid)->where('shield_type',5)->where('shield_id',$this->id)->count();
        $shieldStatus = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id', $mid)->where('shield_type', 5)->where('shield_id', $this->id)->count();
        // 查询 member_shields 表，该评论的作者是否被我屏蔽。输出 0.未屏蔽 1.已屏蔽"
        // $shieldMemberStatus = FresnsMemberShields::where('member_id',$mid)->where('shield_type',1)->where('shield_id',$this->member_id)->count();
        $postAuthorLikeStatus = FresnsMemberLikes::where('member_id', $posts['member_id'])->where('like_type', 5)->where('like_id', $this->id)->count();
        $shieldMemberStatus = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id',
            $mid)->where('shield_type', 1)->where('shield_id', $this->member_id)->count();
        $shieldSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::SHIELD_COMMENT_SETTING);
        $likeSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::LIKE_COMMENT_SETTING);
        $followSetting = ApiConfigHelper::getConfigByItemKey(AmConfig::FOLLOW_COMMENT_SETTING);
        $commentName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', AmConfig::COMMENT_NAME) ?? '评论';
        $followName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', AmConfig::COMMENT_FOLLOW_NAME) ?? '收藏';
        $likeName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', AmConfig::COMMENT_LIKE_NAME) ?? '点赞';
        $shieldName = ApiLanguageHelper::getLanguagesByItemKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', AmConfig::COMMENT_SHIELD_NAME) ?? '不喜欢';
        $likeCount = $this->like_count;
        $commentCount = $this->comment_count;
        $commentLikeCount = $this->comment_like_count;
        $time = DateHelper::asiaShanghaiToTimezone($this->created_at);
        $timeFormat = DateHelper::format_date_langTag(strtotime($time));
        // $timeFormat = str_replace("前", 'ago', $timeFormat);
        // $timeFormat = $this->release_at;
        $editTime = DateHelper::asiaShanghaiToTimezone($this->latest_edit_at);
        $editTimeFormat = '';
        if ($editTime) {
            $editTimeFormat = DateHelper::format_date_langTag(strtotime($editTime));
            // $editTimeFormat = str_replace("前", 'ago', $editTimeFormat);
        }
        $member = [];
        // dd($memberInfo);
        $member['deactivate'] = false;
        $member['isAuthor'] = '';
        $member['mid'] = '';
        $member['mname'] = '';
        $member['nickname'] = '';
        $member['nicknameColor'] = '';
        $member['roleName'] = '';
        $member['roleNameDisplay'] = '';
        $member['roleIcon'] = '';
        $member['roleIconDisplay'] = '';
        $member['avatar'] = $memberInfo->avatar_file_url ?? '';
        // 为空用默认头像
        if (empty($member['avatar'])) {
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
        $member['decorate'] = '';
        $member['gender'] = '';
        $member['bio'] = '';
        $member['verifiedStatus'] = '';
        $member['verifiedIcon'] = '';
        $icons = [];
        $icons['icon'] = '';
        $icons['name'] = '';
        $member['icons'] = $icons;
        if ($this->is_anonymous == 0) {
            if ($memberInfo) {
                if ($memberInfo->deleted_at == null && $memberInfo) {
                    $member['anonymous'] = $this->is_anonymous;
                    $member['deactivate'] = true;
                    $member['isAuthor'] = $this->member_id == $mid ? true : false;
                    $member['mid'] = $memberInfo->uuid ?? '';
                    $member['mname'] = $memberInfo->name ?? '';
                    $member['nickname'] = $memberInfo->nickname ?? '';
                    $member['nicknameColor'] = $memberRole['nickname_color'] ?? '';
                    // "roleName": "主角色的值，如果为不显示则不输出 member_roles > name 多语言",
                    $roleName = '';
                    if (! empty($memberRole)) {
                        $roleName = ApiLanguageHelper::getLanguages(FresnsMemberRolesConfig::CFG_TABLE, 'name',
                            $memberRole['id']);
                        $roleName = $roleName == null ? '' : $roleName['lang_content'];
                    }
                    $member['roleName'] = $roleName;
                    $member['roleNameDisplay'] = $memberRole['is_display_name'] ?? '';
                    $member['roleIcon'] = $memberRole['icon_file_url'] ?? '';
                    $member['roleIconDisplay'] = $memberRole['is_display_icon'] ?? '';
                    // $member['avatar'] = $member['avatar'];

                    $member['decorate'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberInfo->decorate_file_id, $memberInfo->decorate_file_url);
                    $member['gender'] = $memberInfo->gender ?? '';
                    $member['bio'] = $memberInfo->bio ?? '';
                    $member['verifiedStatus'] = $memberInfo->verified_status ?? '';
                    $member['verifiedIcon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberInfo->verified_file_id, $memberInfo->verified_file_url);
                    $icons = [];
                    $icons['icon'] = $memberIcon['icon_file_url'] ?? '';
                    if ($icons['icon']) {
                        $icons['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberIcon['icon_file_id'], $memberIcon['icon_file_url']);
                    }
                    // 多语言 icon  name
                    $icons['name'] = '';

                    if (! empty($memberIcon)) {
                        $iconName = ApiLanguageHelper::getLanguages(FresnsMemberIconsConfig::CFG_TABLE, 'name',
                            $memberIcon['id']);
                        $iconName = $iconName == null ? '' : $iconName['lang_content'];
                        $icons['name'] = $iconName;
                    }
                    if (empty($icons['name']) && empty($icons['icon'])) {
                        $icons = [];
                    }
                    $member['icons'] = $icons;
                }
            }
        }
        // dd($member);
        $commentSetting = []; // 当 searchCid 为空时 commentSetting 才输出。
        $searchCid = request()->input('searchCid');
        // "配置表键名 comment_preview 不为 0 时，代表开启输出，数字代表输出条数，最多 3 条。根据点赞数由大到小输出评论",
        $previewStatus = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_PREVIEW);

        if (! $searchCid) {
            if ($previewStatus && $previewStatus != 0) {
                $commentSetting['status'] = $previewStatus;
                // 该条评论下一共有几条子级评论
                $commentSetting['count'] = FresnsComments::where('parent_id', $this->id)->count();
                $fresnsCommentsService = new FresnsCommentsService();
                $commentList = $fresnsCommentsService->getCommentPreviewList($this->id, $previewStatus, $mid);
                $commentSetting['lists'] = $commentList;
            }
        }
        // 当 searchCid 有值时 replyTo 才输出。代表输出子级评论，只有子级评论才有 replyTo 信息，代表某某回复了某某。
        // 该条评论的 parent_id 为当前评论（参数 searchCid），代表为二级评论，则不输出以下信息。
        // 该条评论的 parent_id 不是当前评论（参数 searchCid），代表为三级或更多级，展现评论下互动，输出他父级评论的以下信息。
        $replyTo = [];
        if ($searchCid) {
            // 获取searchCid 对应的 评论id
            $commentCid = FresnsComments::where('uuid', $searchCid)->first();
            $parentComment = FresnsComments::where('parent_id', $this->id)->first();
            $fresnsCommentsService = new FresnsCommentsService();
            $replyTo = $fresnsCommentsService->getReplyToPreviewList($this->id, $mid);

            // if($parentComment){
            //     if($parentComment['parent_id'] != 0 && ($this->parent_id != 0)){
            //         $parentCommentInfo = FresnsComments::find($this->parent_id);
            //         $parentMemberInfo = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id',$parentCommentInfo['member_id'])->first();
            //         $replyTo['cid'] = $parentCommentInfo['uuid'] ?? "";
            //         $reply['anonymous'] = $parentCommentInfo['is_anonymous'];
            //         $reply['deactivate'] = false;
            //         $replyTo['mid'] = "";
            //         $replyTo['mname'] =  "";
            //         $replyTo['nickname'] = "";
            //         if($parentCommentInfo['is_anonymous'] == 0){
            //             if($parentMemberInfo->deleted_at == null && $parentMemberInfo){
            //                 $reply['deactivate'] = true;
            //                 $replyTo['mid'] = $parentMemberInfo->uuid ?? "";
            //                 $replyTo['mname'] = $parentMemberInfo->name ?? "";
            //                 $replyTo['nickname'] = $parentMemberInfo->nickname ?? "";
            //             }
            //         }
            //     }
            // }
        }

        $location = [];
        $location['isLbs'] = $this->is_lbs;
        $location['mapId'] = $append->map_id ?? '';
        $location['latitude'] = $append->map_latitude ?? '';
        $location['longitude'] = $append->map_longitude ?? '';
        $location['scale'] = $append->map_scale ?? '';
        $location['poi'] = $append->map_poi ?? '';
        $location['poiId'] = $append->map_poi_id ?? '';
        $location['distance'] = '';
        $longitude = request()->input('longitude', '');
        $latitude = request()->input('latitude', '');
        $map_latitude = $location['latitude'] ?? '';
        $map_longitude = $location['longitude'] ?? '';
        if ($longitude && $latitude && $map_latitude && $map_longitude) {
            // 获取单位
            $langTag = $request->header('langTag');
            $distanceUnits = $request->input('lengthUnits');
            if (! $distanceUnits) {
                // 距离
                $languages = ApiConfigHelper::distanceUnits($langTag);
                $distanceUnits = empty($languages) ? 'km' : $languages;
            }

            // dd($languageConfig);
            $location['distance'] = $this->GetDistance($latitude, $longitude, $map_latitude, $map_longitude,
                $distanceUnits);
        }
        $more_json_decode = json_decode($posts['more_json'], true);
        // dd($more_json_decode);
        // $files = ApiFileHelper::getFileInfoByTable(FresnsCommentsConfig::CFG_TABLE,$this->id);
        $files = [];
        $more_json = json_decode($this->more_json, true);
        if ($more_json) {
            $files = ApiFileHelper::getMoreJsonSignUrl($more_json['files']);
        }
        $extends = [];
        // $extendsLinks = Db::table('extend_linkeds')->where('linked_type',2)->where('linked_id',$this->id)->first();
        // if($extendsLinks){
        //     $extendsInfo = FresnsExtends::find($extendsLinks->extend_id);
        // }
        $extendsLinks = Db::table('extend_linkeds')->where('linked_type', 2)->where('linked_id',
            $this->id)->pluck('extend_id')->toArray();
        $extendsInfo = [];
        if ($extendsLinks) {
            $extendsLinks = array_unique($extendsLinks);
            $extendsInfo = FresnsExtends::whereIn('id', $extendsLinks)->get();
        }
        if (! empty($extendsInfo)) {
            foreach ($extendsInfo as $e) {
                $arr = [];
                $arr['eid'] = $e['uuid'] ?? '';
                $arr['plugin'] = $e['plugin_unikey'] ?? '';
                $arr['frame'] = $e['frame'] ?? '';
                $arr['position'] = $e['position'] ?? '';
                $arr['content'] = $e['text_content'] ?? '';
                // $arr['files'] = ApiFileHelper::getFileInfoByTable(FresnsPostsConfig::CFG_TABLE,$this->id);
                if ($arr['frame'] == 1) {
                    $arr['files'] = $e['text_files'];
                }
                $arr['cover'] = $e['cover_file_url'] ?? '';
                if ($arr['cover']) {
                    $arr['cover'] = ApiFileHelper::getImageSignUrlByFileIdUrl($e['cover_file_id'], $e['cover_file_url']);
                }
                $arr['title'] = '';
                if (! empty($e)) {
                    $title = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'title', $e['id']);
                    $title = $title == null ? '' : $title['lang_content'];
                    $arr['title'] = $title;
                }

                $arr['titleColor'] = $e['title_color'] ?? '';
                $arr['descPrimary'] = '';
                if (! empty($e)) {
                    $descPrimary = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'desc_primary',
                        $e['id']);
                    $descPrimary = $descPrimary == null ? '' : $descPrimary['lang_content'];
                    $arr['descPrimary'] = $descPrimary;
                }
                $arr['descPrimaryColor'] = $e['desc_primary_color'] ?? '';
                $arr['descSecondary'] = '';
                if (! empty($e)) {
                    $descSecondary = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'desc_secondary',
                        $e['id']);
                    $descSecondary = $descSecondary == null ? '' : $descSecondary['lang_content'];
                    $arr['descSecondary'] = $descSecondary;
                }

                $arr['descSecondaryColor'] = $e['desc_secondary_color'] ?? '';
                $arr['descPrimaryColor'] = $e['desc_primary_color'] ?? '';
                $arr['btnName'] = '';
                if (! empty($e)) {
                    $btnName = ApiLanguageHelper::getLanguages(FresnsExtendsConfig::CFG_TABLE, 'btn_name', $e['id']);
                    $btnName = $btnName == null ? '' : $btnName['lang_content'];
                    $arr['btnName'] = $btnName;
                }
                $arr['btnColor'] = $e['btn_color'] ?? '';
                $arr['type'] = $e['extend_type'] ?? '';
                $arr['target'] = $e['extend_target'] ?? '';
                $arr['value'] = $e['extend_value'] ?? '';
                $arr['support'] = $e['extend_support'] ?? '';
                $arr['moreJson'] = ApiFileHelper::getMoreJsonSignUrl($e['moreJson']) ?? '';
                $extends[] = $arr;
            }
        }
        $manages = [];
        // attachedQuantity
        $attachCount = [];
        $attachCount['image'] = FresnsFiles::where('file_type', 2)->where('table_name',
            FresnsCommentsConfig::CFG_TABLE)->where('table_id', $this->id)->count();
        $attachCount['videos'] = FresnsFiles::where('file_type', 3)->where('table_name',
            FresnsCommentsConfig::CFG_TABLE)->where('table_id', $this->id)->count();
        $attachCount['audios'] = FresnsFiles::where('file_type', 4)->where('table_name',
            FresnsCommentsConfig::CFG_TABLE)->where('table_id', $this->id)->count();
        $attachCount['docs'] = FresnsFiles::where('file_type', 5)->where('table_name',
            FresnsCommentsConfig::CFG_TABLE)->where('table_id', $this->id)->count();
        $attachCount['extends'] = Db::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('linked_type',
            2)->where('linked_id', $this->id)->count();

        // commentBtn
        $commentBtn = [];
        if ($mid == $this->member_id) {
            $commentBtn['status'] = $postAppends['comment_btn_status'];
            $btnName = ApiLanguageHelper::getLanguages(FresnsPostsConfig::CFG_TABLE, 'comment_btn_name', $posts['id']);
            $btnName = $btnName == null ? '' : $btnName['lang_content'];
            $commentBtn['name'] = $btnName;
            $commentBtn['url'] = $postAppends['comment_btn_plugin_unikey'];
        }

        //  searchPid 为空时输出，代表评论脱离了帖子独立输出，所以需要附带 post 参数，该评论所属帖子的信息
        $searchPid = request()->input('searchPid');
        $post = [];
        if (!$searchPid) {
            $post['pid'] = $posts['uuid'];
            $post['title'] = $posts['title'];
            $post['content'] = $posts['content'];
            $post['status'] = $posts['is_enable'];
            $post['gname'] = "";
            $post['gid'] = "";
            $post['cover'] = "";
            if($groupInfo){
                $gname = ApiLanguageHelper::getLanguages('groups', 'name', $groupInfo['id']);
                $gname = $gname == null ? '' : $gname['lang_content'];
                $post['gname'] = $gname;
                $post['gid'] = $groupInfo['uuid'];
                // $post['cover'] = $groupInfo['cover_file_url'];
                $post['cover'] = ApiFileHelper::getImageSignUrlByFileIdUrl($groupInfo['cover_file_id'], $groupInfo['cover_file_url']);
            }
            
            $post['mid'] = $memberInfo->uuid ?? '';
            $post['mname'] = $memberInfo->name ?? '';
            $post['nickname'] = $memberInfo->nickname ?? '';
            $post['avatar'] = $memberInfo->avatar_file_url ?? '';
            // 为空用默认头像
            if (empty($post['avatar'])) {
                $defaultIcon = ApiConfigHelper::getConfigByItemKey(AmConfig::DEFAULT_AVATAR);
                $post['avatar'] = $defaultIcon;
            }
            // 匿名头像 anonymous_avatar 键值
            if ($this->is_anonymous == 1) {
                $anonymousAvatar = ApiConfigHelper::getConfigByItemKey(AmConfig::ANONYMOUS_AVATAR);
                $post['avatar'] = $anonymousAvatar;
            }
            // 已注销头像 deactivate_avatar 键值"
            if ($memberInfo->deleted_at != null) {
                $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(AmConfig::DEACTIVATE_AVATAR);
                $post['avatar'] = $deactivateAvatar;
            }
            $post['avatar'] = ApiFileHelper::getImageSignUrl($post['avatar']);
        }
        //
        /**1、当 plugin_usages > scene 应用场景不包含「评论」的插件，不输出。
         * 2、当 plugin_usages > is_group_admin 为小组管理员专用，则判断接口请求的成员是否为管理员。
         * 2.1、当 posts > group_id 为空时，代表帖子无小组，小组管理员专用插件无效不输出。
         * 2.2、根据 posts > group_id 和 groups > admin_members 查询该字段中是否含有该成员的 mid，无则不输出。
         */
        //
        //  插件扩展
        $TweetPluginUsages = FresnsPluginUsages::where('type', 5)->where('scene', 'like', '%2%')->first();
        // dd($TweetPluginUsages['plugin_unikey']);
        if ($TweetPluginUsages) {
            $manages['plugin'] = $TweetPluginUsages['plugin_unikey'];
            $plugin = FresnsPlugins::where('unikey', $TweetPluginUsages['plugin_unikey'])->first();
            $name = AmService::getlanguageField('name', $TweetPluginUsages['id']);
            $manages['name'] = $name == null ? '' : $name['lang_content'];
            // $manages['icon'] = $TweetPluginUsages['icon_file_url'];
            $manages['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($TweetPluginUsages['icon_file_id'], $TweetPluginUsages['icon_file_url']);
            $manages['url'] = $plugin['access_path '].'/'.$TweetPluginUsages['parameter'];
            // 是否管理员专用
            // 是否管理员专用
            if ($TweetPluginUsages['is_group_admin'] != 0) {
                // 查询登录用户是否为管理员
                // $roleRels = FresnsMemberRoleRels::where('member_id',$mid)->where('type',2)->pluck('role_id')->toArray();
                // $roles = FresnsMemberRoles::whereIn('id',$roleRels)->where('type',1)->count();
                if (! $posts['group_id']) {
                    $manages = [];
                } else {
                    $groupInfo = FresnsGroups::find($posts['group_id']);
                    if (! $groupInfo) {
                        $manages = [];
                    } else {
                        $permission = json_decode($groupInfo['permission'], true);
                        // dump($permission);
                        // dump($mid);
                        if (isset($permission['admin_members'])) {
                            if (! is_array($permission['admin_members'])) {
                                $manages = [];
                            } else {
                                if (! in_array($mid, $permission['admin_members'])) {
                                    $manages = [];
                                }
                            }
                        } else {
                            $manages = [];
                        }
                    }
                }
            }
            if ($TweetPluginUsages['member_roles']) {
                $mroleRels = FresnsMemberRoleRels::where('member_id', $mid)->first();
                // dd( $mroleRels['role_id']);
                if ($mroleRels) {
                    $pluMemberRoleArr = explode(',', $TweetPluginUsages['member_roles']);
                    // dump($mroleRels);
                    // dd($pluMemberRoleArr);
                    if (! in_array($mroleRels['role_id'], $pluMemberRoleArr)) {
                        $manages = [];
                    }
                }
            }
        }

        // editStatus
        // editStatus
        $editStatus = [];
        // 该篇评论作者是否为本人
        $editStatus['isMe'] = $this->member_id == $mid ? true : false;
        // 评论编辑权限
        $commentEdit = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDIT) ?? false;
        $editTimeRole = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDIT_TIMELIMIT) ?? 5;
        $editSticky = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDIT_STICKY) ?? false;
        if ($commentEdit) {
            // 多长时间内可以编辑
            if (strtotime($this->created_at) + ($editTimeRole * 60) > time()) {
                $commentEdit = false;
            }
            // 帖子置顶后编辑权限
            if ($this->is_sticky != 0) {
                if (! $editSticky) {
                    $commentEdit = false;
                }
            }
        }
        $editStatus['canEdit'] = $commentEdit;
        // dd($postEdit);
        if ($append) {
            $editStatus['canDelete'] = $append->can_delete == 1 ? true : false;
        } else {
            $editStatus['canDelete'] = false;
        }
        // 默认字段
        $default = [
            'pid' => $posts['uuid'],
            'cid' => $cid,
            'content' => $content,
            'brief' => $brief,
            'sticky' => $sticky,
            // 'isLike' => $isLike,
            // 'isShield' => $isShield,
            // 'labelImg' => $labelImg,
            'commentName' => $commentName,
            'likeSetting' => $likeSetting,
            'likeName' => $likeName,
            'likeStatus' => $likeStatus,
            'postAuthorLikeStatus' => $postAuthorLikeStatus,
            'followSetting' => $followSetting,
            'followName' => $followName,
            'followStatus' => $followStatus,
            'shieldSetting' => $shieldSetting,
            'shieldName' => $shieldName,
            'shieldStatus' => $shieldStatus,
            'shieldMemberStatus' => $shieldMemberStatus,
            'likeCount' => $likeCount,
            'followCount' => $this->follow_count,
            'shieldCount' => $this->shield_count,
            'commentCount' => $commentCount,
            'commentLikeCount' => $commentLikeCount,
            'time' => $time,
            'timeFormat' => $timeFormat,
            'editTime' => $editTime,
            'editTimeFormat' => $editTimeFormat,
            'member' => $member,
            'commentSetting' => $commentSetting,
            'replyTo' => $replyTo,
            'location' => $location,
            'attachCount' => $attachCount,
            'files' => $files,
            'extends' => $extends,
            'commentBtn' => $commentBtn,
            'post' => $post,
            'manages' => $manages,
            'editStatus' => $editStatus,
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
