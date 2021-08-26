<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Member;

use App\Helpers\DateHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiFileHelper;
use App\Http\Fresns\FresnsApi\Helpers\ApiLanguageHelper;
use App\Http\Fresns\FresnsCommentLogs\FresnsCommentLogs;
use App\Http\Fresns\FresnsComments\FresnsComments;
use App\Http\Fresns\FresnsConfigs\FresnsConfigsConfig;
use App\Http\Fresns\FresnsLanguages\FresnsLanguagesService;
use App\Http\Fresns\FresnsMemberFollows\FresnsMemberFollows;
use App\Http\Fresns\FresnsMemberIcons\FresnsMemberIcons;
use App\Http\Fresns\FresnsMemberIcons\FresnsMemberIconsConfig;
use App\Http\Fresns\FresnsMemberLikes\FresnsMemberLikes;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRels;
use App\Http\Fresns\FresnsMemberRoleRels\FresnsMemberRoleRelsService;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\Fresns\FresnsMemberRoles\FresnsMemberRolesConfig;
use App\Http\Fresns\FresnsMembers\FresnsMembers;
use App\Http\Fresns\FresnsMemberShields\FresnsMemberShields;
use App\Http\Fresns\FresnsMemberStats\FresnsMemberStats;
use App\Http\Fresns\FresnsPluginBadges\FresnsPluginBadges;
use App\Http\Fresns\FresnsPluginBadges\FresnsPluginBadgesService;
use App\Http\Fresns\FresnsPlugins\FresnsPlugins;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsages;
use App\Http\Fresns\FresnsPluginUsages\FresnsPluginUsagesConfig;
use App\Http\Fresns\FresnsPostLogs\FresnsPostLogs;
use App\Http\Fresns\FresnsPosts\FresnsPosts;
use App\Http\Fresns\FresnsUsers\FresnsUsersConfig;
use Illuminate\Support\Facades\DB;

class AmService
{
    public function common($mid, $langTag, $isMe)
    {
        $seoInfoArr = DB::table('seo')->where('linked_type', 1)->where('linked_id', $mid)->where('deleted_at',
            null)->where('lang_tag', $langTag)->get(['title', 'keywords', 'description'])->first();
        if (empty($seoInfoArr)) {
            $defaultLangTag = ApiLanguageHelper::getDefaultLanguage();
            $seoInfoArr = DB::table('seo')->where('linked_type', 1)->where('linked_id', $mid)->where('deleted_at',
            null)->where('lang_tag', $defaultLangTag)->get(['title', 'keywords', 'description'])->first();
        }
        $data['seoInfo'] = $seoInfoArr;
        //manages
        // plugin_usages > type=5 + scene 字段包含 3
        // plugin_usages > member_roles 为空，则全部输出；有值则判断当前请求成员的所有关联角色 id 是否在字段配置中。
        $pluginUsagesArr = FresnsPluginUsages::where('type', 5)->where('scene', 'LIKE', '%3%')->get()->toArray();
        $managesArr = [];
        if (! empty($pluginUsagesArr)) {
            foreach ($pluginUsagesArr as $v) {
                if (! empty($v['member_roles'])) {
                    $rolesArr = explode(',', $v['member_roles']);
                    if (! in_array($mid, $rolesArr)) {
                        continue;
                    }
                }
                $item = [];
                $item['plugin'] = $v['plugin_unikey'];
                $item['name'] = FresnsLanguagesService::getLanguageByTableId(FresnsPluginUsagesConfig::CFG_TABLE,
                    'name', $v['id'], $langTag);
                $item['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($v['icon_file_id'], $v['icon_file_url']);
                $item['url'] = ApiFileHelper::getPluginUsagesUrl($v['plugin_unikey'], $v['id']);
                $managesArr[] = $item;
            }
        }
        $data['manages'] = $managesArr;

        // plugin_usages > type=7
        // plugin_usages > member_roles 为空，则全部输出；有值则判断当前请求成员的所有关联角色 id 是否在字段配置中。
        // 查看别人信息时不输出。
        $features = [];
        if ($isMe == true) {
            $pluginUsagesArr = FresnsPluginUsages::where('type', 7)->get()->toArray();
            if (! empty($pluginUsagesArr)) {
                foreach ($pluginUsagesArr as $v) {
                    if (! empty($v['member_roles'])) {
                        $rolesArr = explode(',', $v['member_roles']);
                        if (! in_array($mid, $rolesArr)) {
                            continue;
                        }
                    }

                    $item = [];
                    $item['plugin'] = $v['plugin_unikey'];
                    $item['name'] = FresnsLanguagesService::getLanguageByTableId(FresnsPluginUsagesConfig::CFG_TABLE,
                        'name', $v['id'], $langTag);
                    $item['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($v['icon_file_id'], $v['icon_file_url']);
                    $item['url'] = ApiFileHelper::getPluginUsagesUrl($v['plugin_unikey'], $v['id']);
                    $pluginBadges = FresnsPluginBadges::where('plugin_unikey', $v['plugin_unikey'])->where('member_id',
                        $mid)->first();
                    $item['badgesType'] = $pluginBadges['display_type'] ?? '';
                    $item['badgesValue'] = $pluginBadges['value_text'] ?? '';
                    $features[] = $item;
                }
            }
        }

        $data['features'] = $features;

        $profiles = [];
        if ($isMe == true) {
            $pluginUsagesArr = FresnsPluginUsages::where('type', 8)->get()->toArray();
            if (! empty($pluginUsagesArr)) {
                foreach ($pluginUsagesArr as $v) {
                    if (! empty($v['member_roles'])) {
                        $rolesArr = explode(',', $v['member_roles']);
                        if (! in_array($mid, $rolesArr)) {
                            continue;
                        }
                    }
                    $item = [];
                    $item['plugin'] = $v['plugin_unikey'];
                    $item['name'] = FresnsLanguagesService::getLanguageByTableId(FresnsPluginUsagesConfig::CFG_TABLE,
                        'name', $v['id'], $langTag);
                    $item['icon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($v['icon_file_id'], $v['icon_file_url']);
                    $item['url'] = ApiFileHelper::getPluginUsagesUrl($v['plugin_unikey'], $v['id']);
                    $pluginBadges = FresnsPluginBadges::where('plugin_unikey', $v['plugin_unikey'])->where('member_id',
                        $mid)->first();
                    $item['badgesType'] = $pluginBadges['display_type'] ?? '';
                    $item['badgesValue'] = $pluginBadges['value_text'] ?? '';
                    $profiles[] = $item;
                }
            }
        }
        $data['profiles'] = $profiles;

        return $data;
    }

    public static function getMemberList($request)
    {
        $viewMid = $request->input('viewMid');
        $viewType = $request->input('viewType');
        $pageSize = $request->input('pageSize', 20);
        $page = $request->input('page', 1);
        if ($pageSize > 50) {
            $pageSize = 50;
        }
        $query = DB::table('members as me');
        $query = $query->select('me.*')->leftJoin('member_stats as st', 'me.id', '=', 'st.member_id');

        if ($viewType) {
            switch ($viewType) {
                case 1:
                    $memberIdArr = FresnsMemberLikes::where('member_id', $viewMid)->where('like_type',
                        1)->pluck('like_id')->toArray();
                    break;
                case 2:
                    $memberIdArr = FresnsMemberFollows::where('member_id', $viewMid)->where('follow_type',
                        1)->pluck('follow_id')->toArray();
                    break;
                default:
                    $memberIdArr = FresnsMemberShields::where('member_id', $viewMid)->where('shield_type',
                        1)->pluck('shield_id')->toArray();
                    break;
            }
            $query->whereIn('me.id', $memberIdArr);
        }

        $item = $query->paginate($pageSize, ['*'], 'page', $page);

        $data = [];
        $data['list'] = FresnsMemberListsResource::collection($item->items())->toArray($item->items());
        $pagination['total'] = $item->total();
        $pagination['current'] = $page;
        $pagination['pageSize'] = $pageSize;
        $pagination['lastPage'] = $item->lastPage();
        $data['pagination'] = $pagination;

        return $data;
    }

    public function getMemberDetail($mid, $viewMid, $isMe, $langTag)
    {
        $member = FresnsMembers::where('id', $viewMid)->first();

        $data = [];
        if ($member) {
            $data['mid'] = $member['uuid'];
            $data['mname'] = $member['name'];
            $data['nickname'] = $member['nickname'];
            $roleIdArr = FresnsMemberRoleRels::where('member_id', $member['id'])->pluck('role_id')->toArray();
            $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($member['id']);
            $memberRole = FresnsMemberRoles::where('id', $roleId)->first();
            $data['nicknameColor'] = '';
            $data['roleName'] = '';
            $data['roleNameDisplay'] = '';
            $data['roleIcon'] = '';
            $data['roleIconDisplay'] = '';
            if ($memberRole) {
                $data['nicknameColor'] = $memberRole['nickname_color'];
                $data['roleName'] = FresnsLanguagesService::getLanguageByTableId(FresnsMemberRolesConfig::CFG_TABLE,
                    'name', $memberRole['id'], $langTag);
                $data['roleNameDisplay'] = $memberRole['is_display_name'];
                $data['roleIcon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($memberRole['icon_file_id'], $memberRole['icon_file_url']);
                $data['roleIconDisplay'] = $memberRole['icon_display_icon'];
            }
            $users = DB::table(FresnsUsersConfig::CFG_TABLE)->where('id', $member['user_id'])->first();

            if (empty($users->deleted_at)) {
                if (empty($member['avatar_file_url']) && empty($member['avatar_file_id'])) {
                    $defaultAvatar = ApiConfigHelper::getConfigByItemKey('default_avatar');
                    $memberAvatar = ApiFileHelper::getImageSignUrl($defaultAvatar);
                } else {
                    $memberAvatar = ApiFileHelper::getImageSignUrlByFileIdUrl($member['avatar_file_id'], $member['avatar_file_url']);
                }
            } else {
                $deactivateAvatar = ApiConfigHelper::getConfigByItemKey('deactivate_avatar');
                $memberAvatar = ApiFileHelper::getImageSignUrl($deactivateAvatar);
            }
            $data['avatar'] = $memberAvatar;
            $data['decorate'] = ApiFileHelper::getImageSignUrlByFileIdUrl($member['decorate_file_id'],
                $member['decorate_file_url']);
            $data['gender'] = $member['gender'];
            $data['birthday'] = DateHelper::asiaShanghaiToTimezone($member['birthday']);
            $data['bio'] = $member['bio'];
            $data['dialogLimit'] = $member['dialog_limit'];
            $data['timezone'] = $member['timezone'];
            $data['language'] = $member['language'];
            $data['expiredTime'] = DateHelper::asiaShanghaiToTimezone($member['expired_at']);
            $data['verifiedStatus'] = $member['verified_status'];
            $data['verifiedIcon'] = ApiFileHelper::getImageSignUrlByFileIdUrl($member['verified_file_id'],
                $member['verified_file_url']);
            $data['verifiedDesc'] = $member['verified_desc'];
            $data['lastEditMname'] = $member['last_name_at'];
            $data['lastEditNickname'] = $member['last_nickname_at'];
            $data['createdTime'] = DateHelper::asiaShanghaiToTimezone($member['created_at']);
            $data['status'] = $member['is_enable'];
            $memberRolesArr = FresnsMemberRoles::whereIn('id', $roleIdArr)->get()->toArray();
            $rolesArr = [];
            foreach ($memberRolesArr as $v) {
                $item = [];
                $item['type'] = FresnsMemberRoleRels::where('member_id', $mid)->where('role_id',
                    $v['id'])->value('type');
                $item['name'] = FresnsLanguagesService::getLanguageByTableId(FresnsMemberRolesConfig::CFG_TABLE, 'name',
                    $v['id'], $langTag);
                $item['icon'] = $v['icon_file_url'];
                $item['nicknameColor'] = $v['nickname_color'];
                $item['permission'] = json_decode($v['permission'], true);
                $rolesArr[] = $item;
            }
            $data['roles'] = $rolesArr;
            $memberStats = FresnsMemberStats::where('member_id', $viewMid)->first();
            $stats['likeMemberCount'] = $memberStats['like_member_count'] ?? 0;
            $stats['likeGroupCount'] = $memberStats['like_group_count'] ?? 0;
            $stats['likeHashtagCount'] = $memberStats['like_hashtag_count'] ?? 0;
            $stats['likePostCount'] = $memberStats['like_post_count'] ?? 0;
            $stats['likeCommentCount'] = $memberStats['like_comment_count'] ?? 0;
            $stats['followMemberCount'] = $memberStats['follow_member_count'] ?? 0;
            $stats['followGroupCount'] = $memberStats['follow_group_count'] ?? 0;
            $stats['followHashtagCount'] = $memberStats['follow_hashtag_count'] ?? 0;
            $stats['followPostCount'] = $memberStats['follow_post_count'] ?? 0;
            $stats['followCommentCount'] = $memberStats['follow_comment_count'] ?? 0;
            $stats['shieldMemberCount'] = $memberStats['shield_member_count'] ?? 0;
            $stats['shieldGroupCount'] = $memberStats['shield_group_count'] ?? 0;
            $stats['shieldHashtagCount'] = $memberStats['shield_hashtag_count'] ?? 0;
            $stats['shieldPostCount'] = $memberStats['shield_post_count'] ?? 0;
            $stats['shieldCommentCount'] = $memberStats['shield_comment_count'] ?? 0;
            $stats['likeMeCount'] = $memberStats['like_me_count'] ?? 0;
            $stats['followMeCount'] = $memberStats['follow_me_count'] ?? 0;
            $stats['shieldMeCount'] = $memberStats['shield_me_count'] ?? 0;
            $stats['postPublishCount'] = $memberStats['post_publish_count'] ?? 0;
            $stats['postLikeCount'] = $memberStats['post_like_count'] ?? 0;
            $stats['commentPublishCount'] = $memberStats['comment_publish_count'] ?? 0;
            $stats['commentLikeCount'] = $memberStats['comment_like_count'] ?? 0;
            $stats['extcredits1Status'] = ApiConfigHelper::getConfigByItemKey('extcredits1_status');
            $stats['extcredits1Name'] = ApiConfigHelper::getConfigByItemKey('extcredits1_name');
            $stats['extcredits1Unit'] = ApiConfigHelper::getConfigByItemKey('extcredits1_unit');
            if ($stats['extcredits1Status'] == 3) {
                $stats['extcredits1'] = $memberStats['extcredits1'];
            }
            $stats['extcredits2Status'] = ApiConfigHelper::getConfigByItemKey('extcredits2_status');
            $stats['extcredits2Name'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'extcredits2_name', $langTag);
            $stats['extcredits2Unit'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'extcredits2_unit', $langTag);
            if ($stats['extcredits2Status'] == 3) {
                $stats['extcredits2'] = $memberStats['extcredits2'];
            }
            $stats['extcredits3Status'] = ApiConfigHelper::getConfigByItemKey('extcredits3_status');
            $stats['extcredits3Name'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'extcredits3_name', $langTag);
            $stats['extcredits3Unit'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'extcredits3_unit', $langTag);
            if ($stats['extcredits3Status'] == 3) {
                $stats['extcredits3'] = $memberStats['extcredits3'];
            }
            $stats['extcredits4Status'] = ApiConfigHelper::getConfigByItemKey('extcredits4_status');
            $stats['extcredits4Name'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'extcredits4_name', $langTag);
            $stats['extcredits4Unit'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'extcredits4_unit', $langTag);
            if ($stats['extcredits4Status'] == 3) {
                $stats['extcredits4'] = $memberStats['extcredits4'];
            }
            $stats['extcredits5Status'] = ApiConfigHelper::getConfigByItemKey('extcredits5_status');
            $stats['extcredits5Name'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'extcredits5_name', $langTag);
            $stats['extcredits5Unit'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'extcredits5_unit', $langTag);
            if ($stats['extcredits5Status'] == 3) {
                $stats['extcredits5'] = $memberStats['extcredits5'];
            }

            $data['stats'] = $stats;
            $memberIconsArr = FresnsMemberIcons::where('member_id', $viewMid)->get()->toArray();
            $iconsArr = [];
            foreach ($memberIconsArr as $v) {
                $item = [];
                $item['icon'] = $v['icon_file_url'];
                $item['name'] = FresnsLanguagesService::getLanguageByTableId(FresnsMemberIconsConfig::CFG_TABLE, 'name',
                    $v['id'], $langTag);
                $item['type'] = $v['type'];
                $item['url'] = '';
                $iconsArr[] = $item;
            }
            $data['icons'] = $iconsArr;
            $data['draftCount'] = null;
            if ($isMe == true) {
                $draftCount['posts'] = FresnsPostLogs::whereIn('status', [1, 4])->count();
                $draftCount['comments'] = FresnsCommentLogs::whereIn('status', [1, 4])->count();
                $data['draftCount'] = $draftCount;
            }
            $data['memberName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'member_name', $langTag);
            $data['memberIdName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'member_id_name', $langTag);
            $data['memberNameName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'member_name_name', $langTag);
            $data['memberNicknameName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'member_nickname_name', $langTag);
            $data['memberRoleName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'member_role_name', $langTag);
            $data['followSetting'] = ApiConfigHelper::getConfigByItemKey('follow_member_setting');
            $data['followName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'follow_member_name', $langTag);
            if ($isMe == false) {
                $follows = FresnsMemberFollows::where('member_id', $mid)->where('follow_type', 1)->where('follow_id',
                    $viewMid)->first();
                $isFollows = 0;
                if (empty($follows)) {
                    $follows = FresnsMemberFollows::where('member_id', $viewMid)->where('follow_type',
                        1)->where('follow_id', $mid)->first();
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
                $data['followStatus'] = $isFollows;
            }
            $data['likeSetting'] = ApiConfigHelper::getConfigByItemKey('like_member_setting');
            $data['likeName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'like_member_name', $langTag);
            if ($isMe == false) {
                $isLike = 0;
                $count = FresnsMemberLikes::where('member_id', $mid)->where('like_type', 1)->where('like_id',
                    $viewMid)->count();
                if ($count > 0) {
                    $isLike = 1;
                }
                $data['likeStatus'] = $isLike;
            }
            $data['shieldSetting'] = ApiConfigHelper::getConfigByItemKey('shield_member_setting');
            $data['shieldName'] = FresnsLanguagesService::getLanguageByConfigs(FresnsConfigsConfig::CFG_TABLE,
                'item_value', 'shield_member_name', $langTag);
            if ($isMe == false) {
                $isShields = 0;
                $count = FresnsMemberShields::where('member_id', $mid)->where('shield_type', 1)->where('shield_id',
                    $viewMid)->count();
                if ($count > 0) {
                    $isShields = 1;
                }
                $data['shieldStatus'] = $isShields;
            }

            if ($isMe = false) {
                $unikeyArr = FresnsPluginBadges::where('member_id', $mid)->pluck('plugin_unikey')->toArray();
                $managesArr = FresnsPluginUsages::whereIn('plugin_unikey', $unikeyArr)->get()->toArray();
                $expandsArr = [];
                foreach ($managesArr as $v) {
                    $item = [];
                    $item['plugin'] = $v['plugin_unikey'];
                    $item['name'] = FresnsLanguagesService::getLanguageByTableId(FresnsPluginUsagesConfig::CFG_TABLE,
                        'name', $v['id'], $langTag);
                    $item['icon'] = $v['icon_file_url'];
                    $plugins = FresnsPlugins::where('unikey', $v['plugin_unikey'])->first();
                    $item['url'] = $plugins['access_path'].$v['parameter'];

                    $expandsArr[] = $item;
                }
                $data['manages'] = $expandsArr;
            }
        }

        return $data;
    }

    public static function getGroupList($request)
    {
        $viewMid = $request->input('viewMid');
        $viewType = $request->input('viewType');
        // dump($viewMid);
        switch ($viewType) {
            // 点赞的小组
            case 1:
                $groupArr = FresnsMemberLikes::where('member_id', $viewMid)->where('like_type',
                    2)->pluck('like_id')->toArray();
                break;
            case 2:
                $groupArr = FresnsMemberFollows::where('member_id', $viewMid)->where('follow_type',
                    2)->pluck('follow_id')->toArray();
                break;
            default:
                $groupArr = FresnsMemberShields::where('member_id', $viewMid)->where('shield_type',
                    2)->pluck('shield_id')->toArray();
                break;

        }

        return $groupArr;
    }

    public static function getHashtagList($request)
    {
        $viewMid = $request->input('viewMid');
        $viewType = $request->input('viewType');
        // dump($viewMid);
        switch ($viewType) {
            // 点赞的话题
            case 1:
                $hashtagArr = FresnsMemberLikes::where('member_id', $viewMid)->where('like_type',
                    3)->pluck('like_id')->toArray();
                break;
            case 2:
                $hashtagArr = FresnsMemberFollows::where('member_id', $viewMid)->where('follow_type',
                    3)->pluck('follow_id')->toArray();
                break;
            default:
                $hashtagArr = FresnsMemberShields::where('member_id', $viewMid)->where('shield_type',
                    3)->pluck('shield_id')->toArray();
                break;

        }

        return $hashtagArr;
    }

    public static function getPostList($request)
    {
        $viewMid = $request->input('viewMid');
        $viewType = $request->input('viewType');
        // dump($viewMid);
        switch ($viewType) {
            // 点赞的话题
            case 1:
                $postArr = FresnsMemberLikes::where('member_id', $viewMid)->where('like_type',
                    4)->pluck('like_id')->toArray();
                break;
            case 2:
                $postArr = FresnsMemberFollows::where('member_id', $viewMid)->where('follow_type',
                    4)->pluck('follow_id')->toArray();
                break;
            default:
                $postArr = FresnsMemberShields::where('member_id', $viewMid)->where('shield_type',
                    4)->pluck('shield_id')->toArray();
                break;

        }

        return $postArr;
    }

    public static function getCommentList($request)
    {
        $viewMid = $request->input('viewMid');
        $viewType = $request->input('viewType');
        // dump($viewMid);
        switch ($viewType) {
            // 点赞的话题
            case 1:
                $commentArr = FresnsMemberLikes::where('member_id', $viewMid)->where('like_type',
                    5)->pluck('like_id')->toArray();
                break;
            case 2:
                $commentArr = FresnsMemberFollows::where('member_id', $viewMid)->where('follow_type',
                    5)->pluck('follow_id')->toArray();
                break;
            default:
                $commentArr = FresnsMemberShields::where('member_id', $viewMid)->where('shield_type',
                    5)->pluck('shield_id')->toArray();
                break;

        }

        return $commentArr;
    }
}
