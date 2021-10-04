<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Content;

use App\Base\Resources\BaseAdminResource;
use App\Helpers\DateHelper;
use App\Http\Center\Common\GlobalService;
use App\Http\FresnsApi\Helpers\ApiCommonHelper;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsApi\Helpers\ApiFileHelper;
use App\Http\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\FresnsApi\Info\FsService;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsComments\FresnsCommentsConfig;
use App\Http\FresnsDb\FresnsConfigs\FresnsConfigsConfig;
use App\Http\FresnsDb\FresnsDomainLinks\FresnsDomainLinksConfig;
use App\Http\FresnsDb\FresnsExtendLinkeds\FresnsExtendLinkeds;
use App\Http\FresnsDb\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsExtends\FresnsExtendsConfig;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsConfig;
use App\Http\FresnsDb\FresnsImplants\FresnsImplants;
use App\Http\FresnsDb\FresnsImplants\FresnsImplantsConfig;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\FresnsDb\FresnsMemberFollows\FresnsMemberFollowsConfig;
use App\Http\FresnsDb\FresnsMemberIcons\FresnsMemberIcons;
use App\Http\FresnsDb\FresnsMemberIcons\FresnsMemberIconsConfig;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\FresnsDb\FresnsMemberLikes\FresnsMemberLikesConfig;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRolesConfig;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShields;
use App\Http\FresnsDb\FresnsMemberShields\FresnsMemberShieldsConfig;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\FresnsDb\FresnsPluginUsages\FresnsPluginUsagesService;
use App\Http\FresnsDb\FresnsPostAllows\FresnsPostAllowsConfig;
use App\Http\FresnsDb\FresnsPostAppends\FresnsPostAppends;
use App\Http\FresnsDb\FresnsPostAppends\FresnsPostAppendsConfig;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsConfig;
use App\Http\FresnsDb\FresnsPosts\FresnsPostsService;
use Illuminate\Support\Facades\DB;

/**
 * Detail resource config handle.
 */
class FresnsPostsResourceDetail extends BaseAdminResource
{
    public function toArray($request)
    {
        // Form Field

        // Data Table: post_appends
        $append = DB::table(FresnsPostAppendsConfig::CFG_TABLE)->where('post_id', $this->id)->first();
        if ($append) {
            $append = get_object_vars($append);
        }
        // Data Table: members
        $memberInfo = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $this->member_id)->first();
        // Data Table: member_role_rels
        $roleRels = FresnsMemberRoleRels::where('member_id', $this->member_id)->where('type', 2)->first();
        // Data Table: member_roles
        $memberRole = [];
        if (! empty($roleRels)) {
            $memberRole = FresnsMemberRoles::find($roleRels['role_id']);
        }
        // Data Table: member_icons
        $memberIcon = FresnsMemberIcons::where('member_id', $this->member_id)->first();
        // Data Table: comments
        $comments = DB::table('comments as c')->select('c.*')
            ->leftJoin('members as m', 'c.member_id', '=', 'm.id')
            ->where('c.post_id', $this->id)
            ->where('m.deleted_at', null)
            ->where('c.deleted_at', null)
            ->orderby('like_count', 'Desc')
            ->first();
        // Data Table: groups
        $groupInfo = FresnsGroups::find($this->group_id);

        // Post Info
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
        $content = FresnsPostsResource::getContentView(($this->content), ($this->id), 1);
        // Read permission required or not
        $allowStatus = $this->is_allow;
        $allowProportion = 10;
        $noAllow = 0;
        if ($allowStatus == 1) {
            $memberCount = DB::table(FresnsPostAllowsConfig::CFG_TABLE)->where('post_id', $this->id)->where('type', 1)->where('object_id', $mid)->count();
            $memberoleCount = 0;
            if (! empty($roleRels)) {
                $memberoleCount = DB::table(FresnsPostAllowsConfig::CFG_TABLE)->where('post_id', $this->id)->where('type', 2)->where('object_id', $roleRels['role_id'])->count();
            }
            // Read access
            if ($memberCount > 0 || $memberoleCount > 0) {
                $allowStatus = 1;
                $allowProportion = 100;
                $noAllow = 1;
            } else {
                $allowProportion = $append['allow_proportion'];
                $FresnsPostsService = new FresnsPostsService();
                // Prevent @, hashtags, emojis, links and other messages from being truncated
                $contentInfo = $FresnsPostsService->truncatedContentInfo($append['content']);
                $content = FresnsPostsResource::getContentView(($this->content), ($this->id), 1);

                $allowStatus = 0;
            }
        } else {
            $noAllow = 1;
        }
        $brief = $this->is_brief;
        $sticky = $this->sticky_state;
        $essence = $this->essence_state;

        // Operation behavior status
        $likeStatus = DB::table(FresnsMemberLikesConfig::CFG_TABLE)->where('member_id', $mid)->where('like_type', 4)->where('like_id', $this->id)->count();
        $followStatus = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id', $mid)->where('follow_type', 4)->where('follow_id', $this->id)->count();
        $shieldStatus = DB::table(FresnsMemberShieldsConfig::CFG_TABLE)->where('member_id', $mid)->where('shield_type', 4)->where('shield_id', $this->id)->count();
        // Operation behavior settings
        $likeSetting = ApiConfigHelper::getConfigByItemKey(FsConfig::LIKE_POST_SETTING);
        $followSetting = ApiConfigHelper::getConfigByItemKey(FsConfig::FOLLOW_POST_SETTING);
        $shieldSetting = ApiConfigHelper::getConfigByItemKey(FsConfig::SHIELD_POST_SETTING);
        // Operation behavior naming
        $likeName = ApiLanguageHelper::getLanguagesByTableKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', FsConfig::LIKE_POST_NAME) ?? 'Like';
        $followName = ApiLanguageHelper::getLanguagesByTableKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', FsConfig::FOLLOW_POST_NAME) ?? 'Save post';
        $shieldName = ApiLanguageHelper::getLanguagesByTableKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', FsConfig::SHIELD_POST_NAME) ?? 'Hide post';
        // Content Naming
        $PostName = ApiLanguageHelper::getLanguagesByTableKey(FresnsConfigsConfig::CFG_TABLE, 'item_value', FsConfig::POST_NAME) ?? 'Post';

        $viewCount = $this->view_count;
        $likeCount = $this->like_count;
        $followCount = $this->follow_count;
        $shieldCount = $this->shield_count;
        $commentCount = $this->comment_count;
        $commentLikeCount = $this->comment_like_count;
        $time = DateHelper::fresnsOutputTimeToTimezone($this->created_at);
        $timeFormat = DateHelper::format_date_langTag(strtotime($time));
        $editTime = DateHelper::fresnsOutputTimeToTimezone($this->latest_edit_at);
        $editTimeFormat = DateHelper::format_date_langTag(strtotime($editTime));
        $canDelete = $append['can_delete'];
        $allowStatus = $this->is_allow;
        $allowBtnName = ApiLanguageHelper::getLanguagesByTableId(FresnsPostsConfig::CFG_TABLE, 'allow_btn_name', $this->id);
        $allowBtnName = $allowBtnName == null ? '' : $allowBtnName['lang_content'];
        $allowBtnUrl = $append['allow_plugin_unikey'];
        $memberListName = ApiLanguageHelper::getLanguagesByTableId(FresnsPostsConfig::CFG_TABLE, 'member_list_name', $this->id);
        $memberListName = $memberListName == null ? '' : $memberListName['lang_content'];
        $memberListCount = Db::table('post_members')->where('post_id', $this->id)->count();
        $member = [];
        $member['anonymous'] = $this->is_anonymous;
        $member['deactivate'] = false;
        $member['mid'] = '';
        $member['mname'] = '';
        $member['nickname'] = '';
        $member['nicknameColor'] = '';
        $member['roleName'] = '';
        $member['roleNameDisplay'] = '';
        $member['roleIcon'] = '';
        $member['roleIconDisplay'] = '';
        $member['avatar'] = $memberInfo->avatar_file_url ?? '';

        // Default avatar when members have no avatar
        if (empty($member['avatar_file_url'])) {
            $defaultIcon = ApiConfigHelper::getConfigByItemKey(FsConfig::DEFAULT_AVATAR);
            $member['avatar'] = $defaultIcon;
        }
        // Anonymous content for avatar
        if ($this->is_anonymous == 1) {
            $anonymousAvatar = ApiConfigHelper::getConfigByItemKey(FsConfig::ANONYMOUS_AVATAR);
            $member['avatar'] = $anonymousAvatar;
        }
        // The avatar displayed when a member has been deleted
        if ($memberInfo) {
            if ($memberInfo->deleted_at != null) {
                $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(FsConfig::DEACTIVATE_AVATAR);
                $member['avatar'] = $deactivateAvatar;
            }
        } else {
            $deactivateAvatar = ApiConfigHelper::getConfigByItemKey(FsConfig::DEACTIVATE_AVATAR);
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
            if ($memberInfo->deleted_at == null && $memberInfo) {
                $member['anonymous'] = $this->is_anonymous;
                $member['deactivate'] = true;
                $member['mid'] = $memberInfo->uuid ?? '';
                $member['mname'] = $memberInfo->name ?? '';
                $member['nickname'] = $memberInfo->nickname ?? '';
                $member['nicknameColor'] = $memberRole['nickname_color'] ?? '';

                $roleName = '';
                if (! empty($memberRole)) {
                    $roleName = ApiLanguageHelper::getLanguagesByTableId(FresnsMemberRolesConfig::CFG_TABLE, 'name',
                        $memberRole['id']);
                    $roleName = $roleName == null ? '' : $roleName['lang_content'];
                }
                $member['roleName'] = $roleName;
                $member['roleNameDisplay'] = $memberRole['is_display_name'] ?? '';
                $member['roleIcon'] = $memberRole['icon_file_url'] ?? '';
                $member['roleIconDisplay'] = $memberRole['is_display_icon'] ?? '';

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
                $icons['name'] = '';
                if ($memberIcon) {
                    $iconName = ApiLanguageHelper::getLanguagesByTableId(FresnsMemberIconsConfig::CFG_TABLE, 'name', $memberIcon['id']);
                    $iconName = $iconName == null ? '' : $iconName['lang_content'];
                    $icons['name'] = $iconName;
                }
                if (empty($icons['name']) && empty($icons['icon'])) {
                    $icons = [];
                }
                $member['icons'] = $icons;
            }
        }

        // Post Hot
        $postHotStatus = ApiConfigHelper::getConfigByItemKey(FsConfig::POST_HOT);
        $postHotStatus = $postHotStatus == null ? 0 : $postHotStatus;
        $comment = [];
        $location = [];
        $location['isLbs'] = $this->is_lbs;
        $location['mapId'] = $this->map_id;
        $location['latitude'] = $this->map_latitude;
        $location['longitude'] = $this->map_longitude;
        $location['scale'] = $append['map_scale'];
        $location['poi'] = $append['map_poi'];
        $location['poiId'] = $append['map_poi_id'];
        $location['distance'] = '';
        $longitude = request()->input('longitude', '');
        $latitude = request()->input('latitude', '');
        $langTag = request()->header('langTag', '');
        if ($longitude && $latitude && $this->map_latitude && $this->map_longitude) {
            // Get location units
            $distanceUnits = request()->input('lengthUnits');
            if (! $distanceUnits) {
                // Distance
                $languages = ApiConfigHelper::distanceUnits($langTag);
                $distanceUnits = empty($languages) ? 'km' : $languages;
            }
            $location['distance'] = $this->GetDistance($latitude, $longitude, $this->map_latitude, $this->map_longitude, $distanceUnits);
        }

        // Attached Quantity
        $attachCount = [];
        // posts > more_json > files
        $attachCount['images'] = 0;
        $attachCount['videos'] = 0;
        $attachCount['audios'] = 0;
        $attachCount['docs'] = 0;
        $attachCount['extends'] = DB::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('linked_type', 2)->where('linked_id', $this->id)->count();
        $more_json_decode = json_decode($this->more_json, true);
        if ($more_json_decode) {
            if (isset($more_json_decode['files'])) {
                foreach ($more_json_decode['files'] as $m) {
                    if ($m['type'] == 1) {
                        $attachCount['images']++;
                    }
                    if ($m['type'] == 2) {
                        $attachCount['videos']++;
                    }
                    if ($m['type'] == 3) {
                        $attachCount['audios']++;
                    }
                    if ($m['type'] == 4) {
                        $attachCount['docs']++;
                    }
                }
            }
        }

        // Files
        $files = [];

        // Extends
        $extends = [];
        $extendsLinks = Db::table('extend_linkeds')->where('linked_type', 1)->where('linked_id', $this->id)->first();
        $extendsLinks = [];
        if ($extendsLinks) {
            $extendsLinks = array_unique($extendsLinks);
            $extendsInfo = FresnsExtends::whereIn('id', $extendsLinks)->get();
        }
        if ($noAllow != 0) {
            $more_json = json_decode($this->more_json, true);
            if ($more_json) {
                $files = ApiFileHelper::getMoreJsonSignUrl($more_json['files']);
            }
            if (! empty($extendsInfo)) {
                $extends = [];
                foreach ($extendsInfo as $e) {
                    $arr = [];
                    $arr['eid'] = $e['uuid'] ?? '';
                    $arr['plugin'] = $e['plugin_unikey'] ?? '';
                    $arr['frame'] = $e['frame'] ?? '';
                    $arr['position'] = $e['position'] ?? '';
                    $arr['content'] = $e['text_content'] ?? '';
                    if ($arr['frame'] == 1) {
                        $arr['files'] = $e['text_files'];
                    }
                    $arr['cover'] = $e['cover_file_url'] ?? '';
                    if ($arr['cover']) {
                        $arr['cover'] = ApiFileHelper::getImageSignUrlByFileIdUrl($e['cover_file_id'], $e['cover_file_url']);
                    }
                    $arr['title'] = '';
                    if (! empty($e)) {
                        $title = ApiLanguageHelper::getLanguagesByTableId(FresnsExtendsConfig::CFG_TABLE, 'title', $e['id']);
                        $title = $title == null ? '' : $title['lang_content'];
                        $arr['title'] = $title;
                    }
                    $arr['titleColor'] = $e['title_color'] ?? '';
                    $arr['descPrimary'] = '';
                    if (! empty($e)) {
                        $descPrimary = ApiLanguageHelper::getLanguagesByTableId(FresnsExtendsConfig::CFG_TABLE, 'desc_primary', $e['id']);
                        $descPrimary = $descPrimary == null ? '' : $descPrimary['lang_content'];
                        $arr['descPrimary'] = $descPrimary;
                    }
                    $arr['descPrimaryColor'] = $e['desc_primary_color'] ?? '';
                    $arr['descSecondary'] = '';
                    if (! empty($e)) {
                        $descSecondary = ApiLanguageHelper::getLanguagesByTableId(FresnsExtendsConfig::CFG_TABLE, 'desc_secondary', $e['id']);
                        $descSecondary = $descSecondary == null ? '' : $descSecondary['lang_content'];
                        $arr['descSecondary'] = $descSecondary;
                    }
                    $arr['descSecondaryColor'] = $e['desc_secondary_color'] ?? '';
                    $arr['descPrimaryColor'] = $e['desc_primary_color'] ?? '';
                    $arr['btnName'] = '';
                    if (! empty($e)) {
                        $btnName = ApiLanguageHelper::getLanguagesByTableId(FresnsExtendsConfig::CFG_TABLE, 'btn_name', $e['id']);
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
        }

        // Group
        $group = [];
        if ($groupInfo) {
            $group['gid'] = $groupInfo['uuid'] ?? '';
            $name = ApiLanguageHelper::getLanguagesByTableId(FresnsGroupsConfig::CFG_TABLE, 'name', $this->group_id);
            $group['gname'] = $name == null ? '' : $name['lang_content'];
            $description = ApiLanguageHelper::getLanguagesByTableId(FresnsGroupsConfig::CFG_TABLE, 'description', $this->group_id);
            $group['description'] = $description == null ? '' : $description['lang_content'];
            $group['cover'] = $groupInfo['cover_file_url'] ?? '';
            // Whether the current member has the right to comment in the group
            $permission = $groupInfo['permission'] ?? '';
            $permissionArr = json_decode($permission, true);
            $group['allow'] = true;
            if ($permissionArr) {
                $publish_comment = $permissionArr['publish_comment'];
                $publish_post = $permissionArr['publish_post'];
                $publish_comment_roles = $permissionArr['publish_comment_roles'];
                $group['allow'] = false;
                // 1.All Members
                if ($publish_comment == 1) {
                    $group['allow'] = true;
                }
                // 2.Anyone in the group
                if ($publish_comment == 2) {
                    $followCount = DB::table(FresnsMemberFollowsConfig::CFG_TABLE)->where('member_id',
                        $mid)->where('follow_type', 2)->where('follow_id', $this->group_id)->count();
                    if ($followCount > 0) {
                        $group['allow'] = true;
                    }
                }
                // 3.Specified role members only
                if ($publish_post == 3) {
                    $memberRoleArr = FresnsMemberRoleRels::where('member_id', $mid)->pluck('role_id')->toArray();
                    $arrIntersect = array_intersect($memberRoleArr, $publish_comment_roles);
                    if ($arrIntersect) {
                        $group['allow'] = true;
                    }
                }
            }
            $group['viewCount'] = $groupInfo['view_count'] ?? '';
            $group['likeCount'] = $groupInfo['like_count'] ?? '';
            $group['followCount'] = $groupInfo['follow_count'] ?? '';
            $group['shieldCount'] = $groupInfo['shield_count'] ?? '';
            $group['postCount'] = $groupInfo['post_count'] ?? '';
            $group['essenceCount'] = $groupInfo['essence_count'] ?? '';
        }

        // Post Plugin Extensions
        $managesArr = [];
        $FsPluginUsagesArr = FresnsPluginUsages::where('type', 5)->where('scene', 'like', '%1%')->get();
        if ($FsPluginUsagesArr) {
            foreach ($FsPluginUsagesArr as $FsPluginUsages) {
                $manages = [];
                $manages['plugin'] = $FsPluginUsages['plugin_unikey'];
                $plugin = FresnsPlugins::where('unikey', $FsPluginUsages['plugin_unikey'])->first();
                $name = FsService::getLanguageField('name', $FsPluginUsages['id']);
                $manages['name'] = $name == null ? '' : $name['lang_content'];
                $manages['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($FsPluginUsages['icon_file_id'], $FsPluginUsages['icon_file_url']);
                $manages['url'] = ApiCommonHelper::getPluginUsagesUrl($FsPluginUsages['plugin_unikey'], $FsPluginUsages['id']);
                // Is the group administrator dedicated
                if ($FsPluginUsages['is_group_admin'] != 0) {
                    // Query whether the current member is a group administrator
                    if (! $this->group_id) {
                        $manages = [];
                    } else {
                        $groupInfo = FresnsGroups::find($this->group_id);
                        if (! $groupInfo) {
                            $manages = [];
                        } else {
                            $permission = json_decode($groupInfo['permission'], true);
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
                // Determine if the primary role of the current member is an administrator
                if ($FsPluginUsages['member_roles']) {
                    $mroleRels = FresnsMemberRoleRels::where('member_id', $mid)->first();
                    if ($mroleRels) {
                        $pluMemberRoleArr = explode(',', $FsPluginUsages['member_roles']);
                        if (! in_array($mroleRels['role_id'], $pluMemberRoleArr)) {
                            $manages = [];
                        }
                    }
                }
                $managesArr[] = $manages;
            }
        }

        // Edit Status
        $editStatus = [];
        // Is the current member an author
        $editStatus['isMe'] = $this->member_id == $mid ? true : false;
        // Post editing privileges
        $postEdit = ApiConfigHelper::getConfigByItemKey(FsConfig::POST_EDIT) ?? false;
        $editTimes = ApiConfigHelper::getConfigByItemKey(FsConfig::POST_EDIT_TIMELIMIT) ?? 5;
        $editSticky = ApiConfigHelper::getConfigByItemKey(FsConfig::POST_EDIT_STICKY) ?? false;
        $editEssence = ApiConfigHelper::getConfigByItemKey(FsConfig::POST_EDIT_ESSENCE) ?? false;
        if ($postEdit) {
            // How long you can edit
            if (strtotime($this->created_at) + ($editTimes * 60) > time()) {
                $postEdit = false;
            }
            // Post top edit permission
            if ($this->sticky_state != 0) {
                if (! $editSticky) {
                    $postEdit = false;
                }
            }
            // Post editing privileges after adding essence
            if ($this->essence_state != 0) {
                if (! $editEssence) {
                    $postEdit = false;
                }
            }
        }
        $editStatus['canEdit'] = $postEdit;

        // Delete Status
        $editStatus['canDelete'] = $append['can_delete'] == 1 ? true : false;
        if (! $langTag) {
            $langTag = FresnsPluginUsagesService::getDefaultLanguage();
        }

        // SEO Info
        $seo = DB::table('seo')->where('linked_type', 4)->where('linked_id', $this->id)->where('lang_tag', $langTag)->where('deleted_at', null)->first();
        $seoInfo = [];
        if ($seo) {
            $seoInfo['title'] = $seo->title;
            $seoInfo['keywords'] = $seo->keywords;
            $seoInfo['description'] = $seo->description;
        }

        // more_json
        $more_json = json_decode($this->more_json, true);
        $icons = $more_json['icons'] ?? [];

        // Default Field
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
            'postName' => $PostName,
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
            'attachCount' => $attachCount,
            'files' => $files,
            'extends' => $extends,
            'group' => $group,
            'manages' => $managesArr,
            'editStatus' => $editStatus,
            // 'seoInfo' => $seoInfo
        ];

        // Merger
        $arr = $default;

        return $arr;
    }

    // Distance Conversion
    public function GetDistance($lat1, $lng1, $lat2, $lng2, $distanceUnits)
    {
        $EARTH_RADIUS = 6378.137;

        $radLat1 = $this->rad($lat1);
        $radLat2 = $this->rad($lat2);
        $a = $radLat1 - $radLat2;
        $b = $this->rad($lng1) - $this->rad($lng2);
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * $EARTH_RADIUS;
        // 1 km=0.621371192237 mi
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
