<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Editor;

use App\Base\Checkers\BaseChecker;
use App\Helpers\StrHelper;
use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\LogService;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsCommentLogs\FresnsCommentLogs;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsGroups\FresnsGroups;
use App\Http\FresnsDb\FresnsGroups\FresnsGroupsService;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRelsService;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRoles;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRolesService;
use App\Http\FresnsDb\FresnsMembers\FresnsMembers;
use App\Http\FresnsDb\FresnsMembers\FresnsMembersConfig;
use App\Http\FresnsDb\FresnsPlugins\FresnsPlugins;
use App\Http\FresnsDb\FresnsPostLogs\FresnsPostLogs;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsStopWords\FresnsStopWords;
use App\Http\FresnsDb\FresnsUsers\FresnsUsersConfig;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AmChecker extends BaseChecker
{
    // Status Code
    const POST_LOGS_EXISTS = 30006;
    const COMMENT_LOGS_EXISTS = 30008;
    const GROUP_EXISTS = 30057;
    const GROUP_TYPE_ERROR = 30058;
    const TITLE_ERROR = 30059;
    const POST_STATUS_2_ERROR = 30060;
    const POST_STATUS_3_ERROR = 30061;
    const COMMENT_STATUS_2_ERROR = 30062;
    const COMMENT_STATUS_3_ERROR = 30063;
    const POST_SUBMIT_STATUS2_ERROR = 30064;
    const POST_SUBMIT_STATUS3_ERROR = 30065;
    const COMMENT_SUBMIT_STATUS2_ERROR = 30066;
    const COMMENT_SUBMIT_STATUS3_ERROR = 30067;
    const POST_CONTENT_WORDS_ERROR = 30068;
    const COMMENT_CONTENT_WORDS_ERROR = 30069;
    const MEMBER_EXPIRED_LOGS_ERROR = 30070;
    const COMMENT_PID_ERROR = 30071;
    const COMMENT_PARENT_ERROR = 30072;
    const TYPE_ERROR = 30073;
    const POSTS_SUBMIT_ERROR = 30052;
    const USER_EXPIRED_ERROR = 30048;
    const USER_BINDING_EMAIL_ERROR = 30049;
    const USER_BINDING_PHONE_ERROR = 30050;
    const USER_BINDING_REAL_NAME_ERROR = 30051;
    const POSTS_UPDATE_ERROR = 30053;
    const COMMENTS_UPDATE_ERROR = 30055;
    const COMMENTS_SUBMIT_ERROR = 30054;
    const USER_ERROR = 30056;
    const FILE_OR_FILEINFO_ERROR = 30092;

    const PERMISSION_NO_SETTING_ERROR = 30101;
    const SUBMIT_NO_ERROR = 30102;
    const SUBMIT_LIMIT_ERROR = 30103;
    const EDIT_TOP_ERROR = 30104;
    const EDIT_TIME_ERROR = 30105;
    const EDIT_ESSENCE_ERROR = 30106;
    const MEMBER_ROLE_SUBMIT_NO_ERROR = 30107;
    const MEMBER_ROLE_SUBMIT_LIMIT_ERROR = 30108;
    const MEMBER_ROLE_USER_BINDING_EMAIL_ERROR = 30109;
    const MEMBER_ROLE_USER_BINDING_PHONE_ERROR = 30110;
    const MEMBER_ROLE_USER_BINDING_REAL_NAME_ERROR = 30111;
    const POST_MEMBER_ERROR = 30112;
    const PLUGIN_ERROR = 30113;
    const MEMBER_JSON_ERROR = 30114;
    const ALLOW_JSON_ERROR = 30115;
    const LOCATION_JSON_ERROR = 30116;
    const FILE_JSON_ERROR = 30117;
    const EXTENDS_JSON_ERROR = 30118;
    const COMMENT_JSON_ERROR = 30119;
    const COMMENT_APPEND_ERROR = 30121;
    const POST_APPEND_ERROR = 30122;
    const POST_GROUP_ALLOW_ERROR = 30127;
    const POST_COMMENTS_POSTS_ERROR = 30128;
    const POST_COMMENTS_ALLOW_ERROR = 30129;
    const COMMENTS_LOG_EXIST_ERROR = 30130;
    const POSTS_LOG_EXIST_ERROR = 30131;
    const POSTS_LOG_CHECK_PARAMS_ERROR = 30132;
    const EXTENDS_UUID_ERROR = 30133;
    const CONTENT_COUNT_ERROR = 30134;
    const EXTENDS_EID_ERROR = 30138;

    public $codeMap = [
        self::POST_LOGS_EXISTS => '帖子不存在',
        self::COMMENT_LOGS_EXISTS => '评论不存在',
        self::GROUP_EXISTS => '小组不存在',
        self::GROUP_TYPE_ERROR => '小组分类不可发帖',
        self::TITLE_ERROR => '标题过长(应小于255)',
        self::POST_STATUS_2_ERROR => '帖子审核中不可编辑',
        self::POST_STATUS_3_ERROR => '帖子已正式发表不可编辑',
        self::COMMENT_STATUS_2_ERROR => '评论审核中不可编辑',
        self::COMMENT_STATUS_3_ERROR => '评论已正式发表不可编辑',
        self::POST_SUBMIT_STATUS2_ERROR => '处于审核状态的帖子不可提交',
        self::POST_SUBMIT_STATUS3_ERROR => '处于发布状态的帖子不可提交',
        self::COMMENT_SUBMIT_STATUS2_ERROR => '处于审核状态的评论不可提交',
        self::COMMENT_SUBMIT_STATUS3_ERROR => '处于发布状态的评论不可提交',
        self::POST_CONTENT_WORDS_ERROR => '帖子内容里存在违规内容',
        self::COMMENT_CONTENT_WORDS_ERROR => '评论内容里存在违规内容',
        self::MEMBER_EXPIRED_LOGS_ERROR => '成员已过期，不可请求',
        self::COMMENT_PID_ERROR => 'pid required',
        self::COMMENT_PARENT_ERROR => '一级评论才能产生草稿',
        self::TYPE_ERROR => 'type过长',
        self::POSTS_SUBMIT_ERROR => '不允许发布帖子',
        self::USER_EXPIRED_ERROR => '成员已过期',
        self::USER_BINDING_EMAIL_ERROR => '请绑定邮箱',
        self::USER_BINDING_PHONE_ERROR => '请绑定手机号',
        self::USER_BINDING_REAL_NAME_ERROR => '请实名制',
        self::POSTS_UPDATE_ERROR => '不允许编辑帖子',
        self::COMMENTS_UPDATE_ERROR => '不允许编辑评论',
        self::COMMENTS_SUBMIT_ERROR => '不允许发布评论',
        self::USER_ERROR => '用户不存在',
        self::FILE_OR_FILEINFO_ERROR => '文件和文件信息只能传其一',

        self::PERMISSION_NO_SETTING_ERROR => '角色未设置权限',
        self::SUBMIT_NO_ERROR => '未开启发布权限',
        self::SUBMIT_LIMIT_ERROR => '未在指定时间内不允许发布',
        self::EDIT_TOP_ERROR => '置顶后不允许编辑',
        self::EDIT_TIME_ERROR => '超出编辑时间',
        self::EDIT_ESSENCE_ERROR => '加精不允许编辑',
        self::MEMBER_ROLE_SUBMIT_NO_ERROR => '角色未开启发布权限',
        self::MEMBER_ROLE_SUBMIT_LIMIT_ERROR => '角色权限未在指定时间内不允许发布',
        self::MEMBER_ROLE_USER_BINDING_EMAIL_ERROR => '角色开启邮箱校验',
        self::MEMBER_ROLE_USER_BINDING_PHONE_ERROR => '角色开启手机号校验',
        self::MEMBER_ROLE_USER_BINDING_REAL_NAME_ERROR => '角色开启实名制校验',
        self::POST_MEMBER_ERROR => '成员不一致',
        self::PLUGIN_ERROR => '插件不存在或插件未启用',
        self::MEMBER_JSON_ERROR => '请输入正确的memberJson格式',
        self::ALLOW_JSON_ERROR => '请输入正确的allowJson格式',
        self::LOCATION_JSON_ERROR => '请输入正确的locationJson格式',
        self::FILE_JSON_ERROR => '请输入正确的fileJson格式',
        self::EXTENDS_JSON_ERROR => '请输入正确的extendsJson格式',
        self::COMMENT_JSON_ERROR => '请输入正确的commentSetJson格式',
        self::COMMENT_APPEND_ERROR => '评论异常，未找到评论副表记录',
        self::POST_APPEND_ERROR => '帖子异常，未找到帖子副表记录',
        self::POST_GROUP_ALLOW_ERROR => '无小组发帖权限',
        self::POST_COMMENTS_POSTS_ERROR => '评论异常，未找到帖子信息',
        self::POST_COMMENTS_ALLOW_ERROR => '无小组评论权限',
        self::COMMENTS_LOG_EXIST_ERROR => '评论异常，草稿表对应的评论未找到',
        self::POSTS_LOG_EXIST_ERROR => '帖子异常，草稿表对应的帖子未找到',
        self::POSTS_LOG_CHECK_PARAMS_ERROR => '内容、文件、扩展内容，三种不可全部为空，至少其中一个有值',
        self::EXTENDS_UUID_ERROR => '存在未知扩展',
        self::CONTENT_COUNT_ERROR => '内容字数过多',
        self::EXTENDS_EID_ERROR => 'extendsJson eid必填',
    ];

    /**
     * Verify post and comment permissions.
     *
     * @param [type] $type 1-post 2-comment
     * @param [update_type] $update_type 1-add 2-edit
     * @param [userId] $userId
     * @param [memberId] $memberId
     * @param [typeId] $typeId Post or comment id (update_type Must be passed when 2)
     * @return void
     */
    public static function checkPermission($type, $update_type, $userId, $memberId, $typeId = null)
    {
        $uri = Request::getRequestUri();

        $uriRuleArr = AmConfig::URI_NOT_IN_RULE;
        // Verify user and member status
        $user = DB::table(FresnsUsersConfig::CFG_TABLE)->where('id', $userId)->first();
        $member = DB::table(FresnsMembersConfig::CFG_TABLE)->where('id', $memberId)->first();
        $data = [];
        if (empty($user) || empty($member)) {
            return self::checkInfo(self::USER_ERROR);
        }
        switch ($type) {
            case 1:
                switch ($update_type) {
                    case 1:
                        // Verify member role permissions
                        $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($memberId);

                        // Global checksum (post)
                        // Email, Phone number, Real name
                        $post_email_verify = ApiConfigHelper::getConfigByItemKey('post_email_verify');
                        if ($post_email_verify == true) {
                            if (empty($user->email)) {
                                return self::checkInfo(self::USER_BINDING_EMAIL_ERROR);
                            }
                        }
                        $post_phone_verify = ApiConfigHelper::getConfigByItemKey('post_phone_verify');
                        if ($post_phone_verify == true) {
                            if (empty($user->phone)) {
                                return self::checkInfo(self::USER_BINDING_PHONE_ERROR);
                            }
                        }
                        $post_prove_verify = ApiConfigHelper::getConfigByItemKey('post_prove_verify');
                        if ($post_prove_verify == true) {
                            if ($user->prove_verify == 1) {
                                return self::checkInfo(self::USER_BINDING_REAL_NAME_ERROR);
                            }
                        }
                        $post_limit_status = ApiConfigHelper::getConfigByItemKey('post_limit_status');

                        // If the member master role is a whitelisted role, it is not subject to this permission requirement
                        if (in_array($uri, $uriRuleArr)) {
                            if ($post_limit_status == true) {
                                if (! empty($roleId)) {
                                    // Get a list of whitelisted roles
                                    $post_limit_whitelist = ApiConfigHelper::getConfigByItemKey('post_limit_whitelist');
                                    if (! empty($post_limit_whitelist)) {
                                        $post_limit_whitelist_arr = json_decode($post_limit_whitelist, true);
                                        if (in_array($roleId, $post_limit_whitelist_arr)) {
                                            $post_limit_status = false;
                                        }
                                    }
                                }
                            }
                        }

                        // Check Special Rules - Opening Hours
                        if ($post_limit_status === true) {
                            $post_limit_rule = ApiConfigHelper::getConfigByItemKey('post_limit_rule');
                            $post_limit_prompt = ApiConfigHelper::getConfigByItemKey('post_limit_prompt');
                            $post_limit_type = ApiConfigHelper::getConfigByItemKey('post_limit_type');
                            // 1.All-day limit on specified dates
                            if ($post_limit_type == 1) {
                                $post_limit_period_start = ApiConfigHelper::getConfigByItemKey('post_limit_period_start');
                                $post_limit_period_end = ApiConfigHelper::getConfigByItemKey('post_limit_period_end');

                                $time = date('Y-m-d H:i:s', time());

                                if ($post_limit_rule == 2) {
                                    if ($post_limit_period_start <= $time && $post_limit_period_end >= $time) {
                                        return self::checkInfo(ErrorCodeService::SUBMIT_LIMIT_ERROR);
                                    }
                                } else {
                                    if ($post_limit_period_start > $time || $post_limit_period_end < $time) {
                                        return self::checkInfo(ErrorCodeService::SUBMIT_LIMIT_ERROR);
                                    }
                                }
                            }
                            // 2.Specify a time period to set
                            if ($post_limit_type == 2) {
                                $post_limit_cycle_start = ApiConfigHelper::getConfigByItemKey('post_limit_cycle_start');
                                $post_limit_cycle_end = ApiConfigHelper::getConfigByItemKey('post_limit_cycle_end');
                                $post_limit_cycle_start = date('Y-m-d', time()).' '.$post_limit_cycle_start;
                                if ($post_limit_cycle_start < $post_limit_cycle_end) {
                                    $post_limit_cycle_end = date('Y-m-d', time()).' '.$post_limit_cycle_end;
                                } else {
                                    $post_limit_cycle_end = date('Y-m-d',
                                            strtotime('+1 day')).' '.$post_limit_cycle_end;
                                }

                                $time = date('Y-m-d H:i:s', time());

                                if ($post_limit_rule == 2) {
                                    if ($post_limit_cycle_start <= $time && $post_limit_cycle_end >= $time) {
                                        return self::checkInfo(ErrorCodeService::SUBMIT_LIMIT_ERROR);
                                    }
                                } else {
                                    if ($time < $post_limit_cycle_start || $time > $post_limit_cycle_end) {
                                        return self::checkInfo(ErrorCodeService::SUBMIT_LIMIT_ERROR);
                                    }
                                }
                            }
                        }

                        if (empty($roleId)) {
                            return self::checkInfo(ErrorCodeService::PERMISSION_NO_SETTING_ERROR);
                        }

                        $permission = FresnsMemberRoles::where('id', $roleId)->value('permission');
                        if ($permission) {
                            $permissionArr = json_decode($permission, true);
                            if ($permissionArr) {
                                $permissionMap = FresnsMemberRolesService::getPermissionMap($permissionArr);

                                LogService::info('permissionMap-checkPermission', $permissionMap);
                                if (empty($permissionMap)) {
                                    return self::checkInfo(ErrorCodeService::PERMISSION_NO_SETTING_ERROR);
                                }
                                // Publish Post Permissions
                                if ($permissionMap['post_publish'] == false) {
                                    return self::checkInfo(ErrorCodeService::MEMBER_ROLE_SUBMIT_NO_ERROR);
                                }
                                // Publish Post Request - Email
                                if ($permissionMap['post_email_verify'] == true) {
                                    if (empty($user->email)) {
                                        return self::checkInfo(self::MEMBER_ROLE_USER_BINDING_EMAIL_ERROR);
                                    }
                                }
                                // Publish Post Request - Phone Number
                                if ($permissionMap['post_phone_verify'] == true) {
                                    if (empty($user->phone)) {
                                        return self::checkInfo(self::MEMBER_ROLE_USER_BINDING_PHONE_ERROR);
                                    }
                                }
                                // Publish Post Request - Real name
                                if ($permissionMap['post_prove_verify'] == true) {
                                    if ($user->prove_verify == 1) {
                                        return self::checkInfo(self::MEMBER_ROLE_USER_BINDING_REAL_NAME_ERROR);
                                    }
                                }

                                if ($permissionMap['post_limit_status'] == true) {
                                    $post_limit_rule = $permissionMap['post_limit_rule'];
                                    if ($permissionMap['post_limit_type'] == 1) {
                                        $post_limit_period_start = $permissionMap['post_limit_period_start'];
                                        $post_limit_period_end = $permissionMap['post_limit_period_end'];
                                        $time = date('Y-m-d H:i:s', time());
                                        if ($post_limit_rule == 2) {
                                            if ($post_limit_period_start <= $time && $post_limit_period_end >= $time) {
                                                return self::checkInfo(ErrorCodeService::MEMBER_ROLE_SUBMIT_LIMIT_ERROR);
                                            }
                                        } else {
                                            if ($post_limit_period_start > $time || $post_limit_period_end < $time) {
                                                return self::checkInfo(ErrorCodeService::MEMBER_ROLE_SUBMIT_LIMIT_ERROR);
                                            }
                                        }
                                    }
                                    if ($permissionMap['post_limit_type'] == 2) {
                                        $post_limit_cycle_start = $permissionMap['post_limit_cycle_start'];
                                        $post_limit_cycle_end = $permissionMap['post_limit_cycle_end'];
                                        $post_limit_cycle_start = date('Y-m-d', time()).' '.$post_limit_cycle_start;
                                        if ($post_limit_cycle_start < $post_limit_cycle_end) {
                                            $post_limit_cycle_end = date('Y-m-d', time()).' '.$post_limit_cycle_end;
                                        } else {
                                            $post_limit_cycle_end = date('Y-m-d',
                                                    strtotime('+1 day')).' '.$post_limit_cycle_end;
                                        }
                                        $time = date('Y-m-d H:i:s', time());
                                        if ($post_limit_rule == 2) {
                                            if ($post_limit_cycle_start <= $time && $post_limit_cycle_end >= $time) {
                                                return self::checkInfo(ErrorCodeService::MEMBER_ROLE_SUBMIT_LIMIT_ERROR);
                                            }
                                        } else {
                                            if ($time < $post_limit_cycle_start || $time > $post_limit_cycle_end) {
                                                return self::checkInfo(ErrorCodeService::MEMBER_ROLE_SUBMIT_LIMIT_ERROR);
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            return self::checkInfo(ErrorCodeService::PERMISSION_NO_SETTING_ERROR);
                        }

                        break;

                    default:
                        // How long to edit
                        $posts = FresnsPosts::where('id', $typeId)->first();
                        if (! $posts) {
                            return self::checkInfo(self::POSTS_LOG_EXIST_ERROR);
                        }
                        // Whether the member_id and the editor of the post are the same person
                        if ($memberId != $posts['member_id']) {
                            return self::checkInfo(self::POST_MEMBER_ERROR);
                        }
                        $post_edit = ApiConfigHelper::getConfigByItemKey('post_edit');
                        if ($post_edit == false) {
                            return self::checkInfo(self::POSTS_UPDATE_ERROR);
                        }

                        $post_edit_timelimit = ApiConfigHelper::getConfigByItemKey('post_edit_timelimit');
                        $postTime = date('Y-m-d H:i:s', strtotime("+$post_edit_timelimit minutes", strtotime($posts['created_at'])));
                        $time = date('Y-m-d H:i:s', time());
                        if ($postTime < $time) {
                            return self::checkInfo(ErrorCodeService::EDIT_TIME_ERROR);
                        }
                        $post_edit_sticky = ApiConfigHelper::getConfigByItemKey('post_edit_sticky');
                        // Determine edit permissions after post topping
                        if ($posts['sticky_status'] != 1) {
                            if ($post_edit_sticky == false) {
                                return self::checkInfo(ErrorCodeService::EDIT_TOP_ERROR);
                            }
                        }
                        $post_edit_essence = ApiConfigHelper::getConfigByItemKey('post_edit_essence');
                        // Determine the editing permissions after the post is refined
                        if ($posts['essence_status'] != 1) {
                            if ($post_edit_essence == false) {
                                return self::checkInfo(ErrorCodeService::EDIT_ESSENCE_ERROR);
                            }
                        }
                        break;
                }
                break;

            default:
                switch ($update_type) {
                    case 1:
                        $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($memberId);
                        // Publish Comment Request - Email
                        $comment_email_verify = ApiConfigHelper::getConfigByItemKey('comment_email_verify');
                        if ($comment_email_verify == true) {
                            if (empty($user->email)) {
                                return self::checkInfo(self::USER_BINDING_EMAIL_ERROR);
                            }
                        }
                        // Publish Comment Request - Phone Number
                        $comment_phone_verify = ApiConfigHelper::getConfigByItemKey('comment_phone_verify');
                        if ($comment_phone_verify == true) {
                            if (empty($user->phone)) {
                                return self::checkInfo(self::USER_BINDING_PHONE_ERROR);
                            }
                        }
                        // Publish Comment Request - Real name
                        $comment_prove_verify = ApiConfigHelper::getConfigByItemKey('comment_prove_verify');
                        if ($comment_prove_verify == true) {
                            if ($user->prove_verify == 1) {
                                return self::checkInfo(self::USER_BINDING_REAL_NAME_ERROR);
                            }
                        }

                        $comment_limit_status = ApiConfigHelper::getConfigByItemKey('comment_limit_status');
                        // If the member master role is a whitelisted role, it is not subject to this permission requirement
                        if (in_array($uri, $uriRuleArr)) {
                            if ($comment_limit_status == true) {
                                if (! empty($roleId)) {
                                    // Get a list of whitelisted roles
                                    $comment_limit_whitelist = ApiConfigHelper::getConfigByItemKey('comment_limit_whitelist');
                                    if (! empty($comment_limit_whitelist)) {
                                        $comment_limit_whitelist_arr = json_decode($comment_limit_whitelist, true);
                                        if (in_array($roleId, $comment_limit_whitelist_arr)) {
                                            $comment_limit_status = false;
                                        }
                                    }
                                }
                            }
                        }
                        // Check Special Rules - Opening Hours
                        if ($comment_limit_status == true) {
                            $comment_limit_rule = ApiConfigHelper::getConfigByItemKey('comment_limit_rule');
                            $comment_limit_prompt = ApiConfigHelper::getConfigByItemKey('comment_limit_prompt');
                            $comment_limit_type = ApiConfigHelper::getConfigByItemKey('comment_limit_type');
                            // 1.All-day limit on specified dates
                            if ($comment_limit_type == 1) {
                                $comment_limit_period_start = ApiConfigHelper::getConfigByItemKey('comment_limit_period_start');
                                $comment_limit_period_end = ApiConfigHelper::getConfigByItemKey('comment_limit_period_end');
                                $time = date('Y-m-d H:i:s', time());
                                if ($comment_limit_rule == 2) {
                                    if ($comment_limit_period_start <= $time && $comment_limit_period_end >= $time) {
                                        return self::checkInfo(self::SUBMIT_LIMIT_ERROR);
                                    }
                                } else {
                                    if ($time < $comment_limit_period_start || $time > $comment_limit_period_end) {
                                        return self::checkInfo(self::SUBMIT_LIMIT_ERROR);
                                    }
                                }
                            }
                            // 2.Specify a time period to set
                            if ($comment_limit_type == 2) {
                                $comment_limit_cycle_start = ApiConfigHelper::getConfigByItemKey('comment_limit_cycle_start');
                                $comment_limit_cycle_end = ApiConfigHelper::getConfigByItemKey('comment_limit_cycle_end');
                                $comment_limit_cycle_start = date('Y-m-d', time()).' '.$comment_limit_cycle_start;
                                if ($comment_limit_cycle_start < $comment_limit_cycle_end) {
                                    $post_limit_cycle_end = date('Y-m-d', time()).' '.$comment_limit_cycle_end;
                                } else {
                                    $post_limit_cycle_end = date('Y-m-d',
                                            strtotime('+1 day')).' '.$comment_limit_cycle_end;
                                }
                                $time = date('Y-m-d H:i:s', time());

                                if ($comment_limit_rule == 2) {
                                    if ($comment_limit_cycle_start <= $time && $comment_limit_cycle_end >= $time) {
                                        return self::checkInfo(self::SUBMIT_LIMIT_ERROR);
                                    }
                                } else {
                                    if ($time < $comment_limit_cycle_start || $time > $comment_limit_cycle_end) {
                                        return self::checkInfo(self::SUBMIT_LIMIT_ERROR);
                                    }
                                }
                            }
                        }
                        // Global checks pass, checks role permissions
                        if (empty($roleId)) {
                            return self::checkInfo(ErrorCodeService::PERMISSION_NO_SETTING_ERROR);
                        }

                        $permission = FresnsMemberRoles::where('id', $roleId)->value('permission');
                        if ($permission) {
                            $permissionArr = json_decode($permission, true);
                            $permissionMap = FresnsMemberRolesService::getPermissionMap($permissionArr);
                            if (empty($permissionMap)) {
                                return self::checkInfo(ErrorCodeService::PERMISSION_NO_SETTING_ERROR);
                            }

                            // Publish Comment Permissions
                            if ($permissionMap['comment_publish'] == false) {
                                return self::checkInfo(self::MEMBER_ROLE_SUBMIT_NO_ERROR);
                            }

                            // Publish Comment Request - Email
                            if ($permissionMap['comment_email_verify'] == true) {
                                if (empty($user->email)) {
                                    return self::checkInfo(self::MEMBER_ROLE_USER_BINDING_EMAIL_ERROR);
                                }
                            }
                            // Publish Comment Request - Phone Number
                            if ($permissionMap['comment_phone_verify'] == true) {
                                if (empty($user->phone)) {
                                    return self::checkInfo(self::MEMBER_ROLE_USER_BINDING_PHONE_ERROR);
                                }
                            }
                            // Publish Comment Request - Real name
                            if ($permissionMap['comment_prove_verify'] == true) {
                                if ($user->prove_verify == 1) {
                                    return self::checkInfo(self::MEMBER_ROLE_USER_BINDING_REAL_NAME_ERROR);
                                }
                            }

                            // Check Special Rules - Opening Hours
                            if ($permissionMap['comment_limit_status'] == true) {
                                $comment_limit_rule = $permissionMap['comment_limit_rule'];
                                // 1.All-day limit on specified dates
                                if ($comment_limit_type == 1) {
                                    $comment_limit_period_start = $permissionMap['comment_limit_period_start'];
                                    $comment_limit_period_end = $permissionMap['comment_limit_period_end'];
                                    $time = date('Y-m-d H:i:s', time());
                                    if ($comment_limit_rule == 2) {
                                        if ($comment_limit_period_start <= $time && $comment_limit_period_end >= $time) {
                                            return self::checkInfo(self::MEMBER_ROLE_SUBMIT_LIMIT_ERROR);
                                        }
                                    } else {
                                        if ($time < $comment_limit_period_start || $time > $comment_limit_period_end) {
                                            return self::checkInfo(self::MEMBER_ROLE_SUBMIT_LIMIT_ERROR);
                                        }
                                    }
                                }
                                // 2.Specify a time period to set
                                if ($comment_limit_type == 2) {
                                    $comment_limit_cycle_start = $permissionMap['comment_limit_cycle_start'];
                                    $comment_limit_cycle_end = $permissionMap['comment_limit_cycle_end'];
                                    $comment_limit_cycle_start = date('Y-m-d', time()).' '.$comment_limit_cycle_start;
                                    if ($comment_limit_cycle_start < $comment_limit_cycle_end) {
                                        $post_limit_cycle_end = date('Y-m-d', time()).' '.$comment_limit_cycle_end;
                                    } else {
                                        $post_limit_cycle_end = date('Y-m-d',
                                                strtotime('+1 day')).' '.$comment_limit_cycle_end;
                                    }
                                    $time = date('Y-m-d H:i:s', time());

                                    if ($comment_limit_rule == 2) {
                                        if ($comment_limit_cycle_start <= $time && $comment_limit_cycle_end >= $time) {
                                            return self::checkInfo(self::MEMBER_ROLE_SUBMIT_LIMIT_ERROR);
                                        }
                                    } else {
                                        if ($time < $comment_limit_cycle_start || $time > $comment_limit_cycle_end) {
                                            return self::checkInfo(self::MEMBER_ROLE_SUBMIT_LIMIT_ERROR);
                                        }
                                    }
                                }
                            }
                        } else {
                            return self::checkInfo(ErrorCodeService::MEMBER_ROLE_SUBMIT_LIMIT_ERROR);
                        }

                        break;

                    default:
                        // How long to edit
                        $comments = FresnsComments::where('id', $typeId)->first();
                        if (! $comments) {
                            return self::checkInfo(self::COMMENTS_LOG_EXIST_ERROR);
                        }
                        // Whether the member_id and the editor of the comment are the same person
                        if ($memberId != $comments['member_id']) {
                            return self::checkInfo(self::POST_MEMBER_ERROR);
                        }
                        $comment_edit = ApiConfigHelper::getConfigByItemKey('comment_edit');
                        if ($comment_edit == false) {
                            return self::checkInfo(self::COMMENTS_UPDATE_ERROR);
                        }

                        $comment_edit_timelimit = ApiConfigHelper::getConfigByItemKey('comment_edit_timelimit');
                        $commentsTime = date('Y-m-d H:i:s', strtotime("+$comment_edit_timelimit minutes", strtotime($comments['created_at'])));
                        $time = date('Y-m-d H:i:s', time());
                        if ($commentsTime < $time) {
                            return self::checkInfo(self::EDIT_TIME_ERROR);
                        }
                        $comment_edit_sticky = ApiConfigHelper::getConfigByItemKey('comment_edit_sticky');
                        // Determine edit permissions after post topping
                        if ($comments['is_sticky'] == 1) {
                            if ($comment_edit_sticky == false) {
                                return self::checkInfo(self::EDIT_TOP_ERROR);
                            }
                        }
                        break;
                }
                break;
        }

        return true;
    }

    /**
     * Verify global config
     * 1.The posting or commenting requirement is false
     * 2.Special rules not turned on.
     *
     * @param [type] $type 1-post 2-comment
     * @return void
     */
    public static function checkGlobalSubmit($type)
    {
        switch ($type) {
            case 1:
                $post_email_verify = ApiConfigHelper::getConfigByItemKey('post_email_verify');
                $post_phone_verify = ApiConfigHelper::getConfigByItemKey('post_phone_verify');
                $post_prove_verify = ApiConfigHelper::getConfigByItemKey('post_prove_verify');
                $post_limit_status = ApiConfigHelper::getConfigByItemKey('post_limit_status');

                if ($post_email_verify == false && $post_phone_verify == false && $post_prove_verify == false && $post_limit_status == false) {
                    return false;
                }
                break;

            default:
                $comment_email_verify = ApiConfigHelper::getConfigByItemKey('comment_email_verify');
                $comment_phone_verify = ApiConfigHelper::getConfigByItemKey('comment_phone_verify');
                $comment_prove_verify = ApiConfigHelper::getConfigByItemKey('comment_prove_verify');
                $comment_limit_status = ApiConfigHelper::getConfigByItemKey('comment_limit_status');

                if ($comment_email_verify == false && $comment_phone_verify == false && $comment_prove_verify == false && $comment_limit_status == false) {
                    return false;
                }
                break;
        }

        return true;
    }

    /**
     * Check if the current post or comment needs to be moderated.
     *
     * @param [type] $type 1-post 2-comment
     * @param [mid] $mid
     */
    public static function checkAudit($type, $mid, $content)
    {
        $isCheck = false;
        $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($mid);
        // Global special rules configuration (Review requirements)
        if ($type == 1) {
            $post_limit_status = ApiConfigHelper::getConfigByItemKey('post_limit_status');
            if ($post_limit_status == true && $roleId > 0) {
                // Get a list of whitelisted roles
                $post_limit_whitelist = ApiConfigHelper::getConfigByItemKey('post_limit_whitelist');
                if (! empty($post_limit_whitelist)) {
                    $post_limit_whitelist_arr = json_decode($post_limit_whitelist, true);
                    if (in_array($roleId, $post_limit_whitelist_arr)) {
                        $post_limit_status = false;
                    }
                }
            }

            if ($post_limit_status == true) {
                $post_limit_rule = ApiConfigHelper::getConfigByItemKey('post_limit_rule');
                if ($post_limit_rule == 1) {
                    $isCheck = true;
                }
            }
            $contentStopWord = self::stopWords($content);
            if ($contentStopWord == 4) {
                $isCheck = true;
            }
        } else {
            $comment_limit_status = ApiConfigHelper::getConfigByItemKey('comment_limit_status');
            if ($comment_limit_status == true && $roleId > 0) {
                // Get a list of whitelisted roles
                $comment_limit_whitelist = ApiConfigHelper::getConfigByItemKey('comment_limit_whitelist');
                if (! empty($comment_limit_whitelist)) {
                    $comment_limit_whitelist_arr = json_decode($comment_limit_whitelist, true);
                    if (in_array($roleId, $comment_limit_whitelist_arr)) {
                        $comment_limit_status = false;
                    }
                }
            }

            if ($comment_limit_status == true) {
                $comment_limit_rule = ApiConfigHelper::getConfigByItemKey('comment_limit_rule');
                if ($comment_limit_rule == 1) {
                    $isCheck = true;
                }
            }
            $contentStopWord = self::stopWords($content);
            if ($contentStopWord == 4) {
                $isCheck = true;
            }
        }

        if ($roleId) {
            $permission = FresnsMemberRoles::where('id', $roleId)->value('permission');
            if ($permission) {
                $permissionArr = json_decode($permission, true);
                if ($permissionArr) {
                    $permissionMap = FresnsMemberRolesService::getPermissionMap($permissionArr);
                    if ($permissionMap) {
                        if ($type == 1) {
                            $postLimitReview = $permissionMap['post_review'];
                            if ($postLimitReview == true) {
                                $isCheck = true;
                            }
                        } else {
                            $commentLimitReview = $permissionMap['comment_review'];
                            if ($commentLimitReview == true) {
                                $isCheck = true;
                            }
                        }
                    }
                }
            }
        }

        return $isCheck;
    }

    /**
     * Update draft content validation.
     */
    public static function checkDrast($mid)
    {
        $request = request();
        $logType = $request->input('logType');
        $logId = $request->input('logId');
        $gid = $request->input('gid');
        $type = $request->input('type');
        $memberListJson = $request->input('memberListJson');
        $commentSetJson = $request->input('commentSetJson');
        $allowJson = $request->input('allowJson');
        $locationJson = $request->input('locationJson');
        $filesJson = $request->input('filesJson');
        $extendsJson = $request->input('extendsJson');
        if ($memberListJson) {
            $memberListJsonStatus = StrHelper::isJson($memberListJson);
            if (! $memberListJsonStatus) {
                return self::checkInfo(self::MEMBER_JSON_ERROR);
            }
        }
        if ($commentSetJson) {
            $commentSetJsonStatus = StrHelper::isJson($commentSetJson);
            if (! $commentSetJsonStatus) {
                return self::checkInfo(self::COMMENT_JSON_ERROR);
            }
        }
        if ($allowJson) {
            $allowJsonStatus = StrHelper::isJson($allowJson);
            if (! $allowJsonStatus) {
                return self::checkInfo(self::ALLOW_JSON_ERROR);
            }
        }
        if ($locationJson) {
            $locationJsonStatus = StrHelper::isJson($locationJson);
            if (! $locationJsonStatus) {
                return self::checkInfo(self::LOCATION_JSON_ERROR);
            }
        }
        if ($filesJson) {
            $filesJsonStatus = StrHelper::isJson($filesJson);
            if (! $filesJsonStatus) {
                return self::checkInfo(self::FILE_JSON_ERROR);
            }
        }
        if ($extendsJson) {
            $extendsJsonStatus = StrHelper::isJson($extendsJson);
            if (! $extendsJsonStatus) {
                return self::checkInfo(self::EXTENDS_JSON_ERROR);
            }
            $extends = json_decode($extendsJson, true);
            foreach ($extends as $e) {
                if (! isset($e['eid'])) {
                    return self::checkInfo(self::EXTENDS_EID_ERROR);
                } else {
                    if (empty($e['eid'])) {
                        return self::checkInfo(self::EXTENDS_EID_ERROR);
                    }
                }
            }
        }
        $title = $request->input('title');
        $content = $request->input('content');
        // pluginUnikey
        $pluginUnikey = $request->input('pluginUnikey');
        if ($pluginUnikey) {
            $pluginCount = FresnsPlugins::Where('unikey', $pluginUnikey)->where('is_enable', 1)->count();
            if ($pluginCount == 0) {
                return self::checkInfo(self::PLUGIN_ERROR);
            }
        }
        // Site mode verification
        $site_mode = ApiConfigHelper::getConfigByItemKey('site_mode');
        if ($site_mode == AmConfig::PRIVATE) {
            $memberInfo = FresnsMembers::find($mid);
            if ($memberInfo['expired_at'] && ($memberInfo['expired_at'] <= date('Y-m-d H:i:s'))) {
                return self::checkInfo(self::MEMBER_EXPIRED_LOGS_ERROR);
            }
        }
        switch ($logType) {
            case 1:
                $postLogs = FresnsPostLogs::where('id', $logId)->first();
                if (! $postLogs) {
                    return self::checkInfo(self::POST_LOGS_EXISTS);
                }
                // Editable or not
                if ($postLogs['status'] == 2) {
                    return self::checkInfo(self::POST_STATUS_2_ERROR);
                }
                if ($postLogs['status'] == 3) {
                    return self::checkInfo(self::POST_STATUS_3_ERROR);
                }
                // Check whether the gid is correct, including whether the right to post in the group, whether the group can post (group classification is not allowed)
                if (! empty($gid)) {
                    $group = FresnsGroups::where('uuid', $gid)->first();
                    if (! ($group)) {
                        return self::checkInfo(self::GROUP_EXISTS);
                    }
                    if ($group['type'] == 1) {
                        return self::checkInfo(self::GROUP_TYPE_ERROR);
                    }
                }
                // Judgment word limit
                if ($content) {
                    // Get the maximum number of words in a post
                    $postEditorWordCount = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_EDITOR_WORD_COUNT) ?? 1000;
                    if (mb_strlen(trim($content)) > $postEditorWordCount) {
                        return self::checkInfo(self::CONTENT_COUNT_ERROR);
                    }
                }
                break;
            default:
                $commentLogs = FresnsCommentLogs::where('id', $logId)->first();
                if (! $commentLogs) {
                    return self::checkInfo(self::COMMENT_LOGS_EXISTS);
                }
                // Editable or not
                if ($commentLogs['status'] == 2) {
                    return self::checkInfo(self::COMMENT_STATUS_2_ERROR);
                }
                if ($commentLogs['status'] == 3) {
                    return self::checkInfo(self::COMMENT_STATUS_3_ERROR);
                }
                if ($content) {
                    // Get the maximum number of words in a comment
                    $commentEditorWordCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_WORD_COUNT) ?? 1000;
                    if (mb_strlen(trim($content)) > $commentEditorWordCount) {
                        return self::checkInfo(self::CONTENT_COUNT_ERROR);
                    }
                }
                break;
        }
        // The title should not be too long
        if ($title) {
            $strlen = mb_strlen($title);
            if ($strlen > 255) {
                return self::checkInfo(self::TITLE_ERROR);
            }
        }
        // type cannot be too long
        if ($type) {
            $strlen = mb_strlen($type);
            if ($strlen > 128) {
                return self::checkInfo(self::TYPE_ERROR);
            }
        }
    }

    /*
     * Create Draft Check
     */
    public static function checkCreate($mid)
    {
        $request = request();
        $type = $request->input('type');
        $uuid = $request->Input('uuid');
        $pid = $request->input('pid');
        // In case of private mode, the feature cannot be requested when it expires (members > expired_at).
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $memberInfo = FresnsMembers::find($mid);
            if ($memberInfo['expired_at'] && ($memberInfo['expired_at'] <= date('Y-m-d H:i:s'))) {
                return self::checkInfo(self::MEMBER_EXPIRED_LOGS_ERROR);
            }
        }
        switch ($type) {
            case 1:
                if (! empty($uuid)) {
                    $postInfo = FresnsPosts::where('uuid', $uuid)->first();
                    if (! $postInfo) {
                        return self::checkInfo(self::POST_LOGS_EXISTS);
                    }
                    $postCount = DB::table('posts as post')
                        ->join('post_appends as append', 'post.id', '=', 'append.post_id')
                        ->where('post.uuid', $uuid)
                        ->where('post.deleted_at', null)
                        ->count();
                    if ($postCount == 0) {
                        return self::checkInfo(self::POST_APPEND_ERROR);
                    }
                }
                break;

            default:
                if (empty($uuid)) {
                    if (empty($pid)) {
                        return self::checkInfo(self::COMMENT_PID_ERROR);
                    }
                    $postInfo = FresnsPosts::where('uuid', $pid)->first();
                    if (! $postInfo) {
                        return self::checkInfo(self::POST_LOGS_EXISTS);
                    }
                    $postCount = DB::table('posts as post')
                        ->join('post_appends as append', 'post.id', '=', 'append.post_id')
                        ->where('post.uuid', $pid)
                        ->where('post.deleted_at', null)
                        ->count();
                    if ($postCount == 0) {
                        return self::checkInfo(self::POST_APPEND_ERROR);
                    }
                } else {
                    $commentInfo = FresnsComments::where('uuid', $uuid)->first();
                    if (! $commentInfo) {
                        return self::checkInfo(self::COMMENT_LOGS_EXISTS);
                    }
                    $commentCount = DB::table('comments as comment')
                        ->join('comment_appends as append', 'comment.id', '=', 'append.comment_id')
                        ->where('comment.uuid', $uuid)
                        ->where('comment.deleted_at', null)
                        ->count();
                    if ($commentCount == 0) {
                        return self::checkInfo(self::COMMENT_APPEND_ERROR);
                    }
                    if ($commentInfo['parent_id'] != 0) {
                        return self::checkInfo(self::COMMENT_PARENT_ERROR);
                    }
                }
                break;
        }
    }

    /*
     * Submitted Content Check
     */
    public static function checkSubmit($mid)
    {
        $request = request();
        $type = $request->input('type');
        $logId = $request->input('logId');
        // In case of private mode, the feature cannot be requested when it expires (members > expired_at).
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $memberInfo = FresnsMembers::find($mid);
            if ($memberInfo['expired_at'] && ($memberInfo['expired_at'] <= date('Y-m-d H:i:s'))) {
                return self::checkInfo(self::MEMBER_EXPIRED_LOGS_ERROR);
            }
        }
        switch ($type) {
            case 1:
                $postLog = FresnsPostLogs::find($logId);
                if (! $postLog) {
                    return self::checkInfo(self::POST_LOGS_EXISTS);
                }
                if ($postLog['status'] == 2) {
                    return self::checkInfo(self::POST_SUBMIT_STATUS2_ERROR);
                }
                if ($postLog['status'] == 3) {
                    return self::checkInfo(self::POST_SUBMIT_STATUS3_ERROR);
                }
                // Logs have group values to determine whether the group exists and whether current members have the right to post in the group
                if ($postLog['group_id']) {
                    $groupInfo = FresnsGroups::find($postLog['group_id']);
                    if (! $groupInfo) {
                        return self::checkInfo(self::GROUP_EXISTS);
                    }
                    if ($groupInfo['type'] == 1) {
                        return self::checkInfo(self::GROUP_TYPE_ERROR);
                    }
                    $publishRule = FresnsGroupsService::publishRule($mid, $groupInfo['permission'], $groupInfo['id']);
                    if (! $publishRule['allowPost']) {
                        return self::checkInfo(self::POST_GROUP_ALLOW_ERROR);
                    }
                }
                if (empty($postLog['content']) && (empty($postLog['files_json']) || empty(json_decode($postLog['files_json'], true))) && (empty($postLog['extends_json']) || empty(json_decode($postLog['extends_json'], true)))) {
                    return self::checkInfo(self::POSTS_LOG_CHECK_PARAMS_ERROR);
                }
                // Stop Word Rule Check
                if ($postLog['content']) {
                    $message = self::stopWords($postLog['content']);
                    if (! $message) {
                        return self::checkInfo(self::POST_CONTENT_WORDS_ERROR);
                    }
                }
                break;

            default:
                $commentLog = FresnsCommentLogs::find($logId);
                if (! $commentLog) {
                    return self::checkInfo(self::COMMENT_LOGS_EXISTS);
                }
                if ($commentLog['status'] == 2) {
                    return self::checkInfo(self::COMMENT_SUBMIT_STATUS2_ERROR);
                }
                if ($commentLog['status'] == 3) {
                    return self::checkInfo(self::COMMENT_SUBMIT_STATUS3_ERROR);
                }
                $postInfo = FresnsPosts::find($commentLog['post_id']);
                if (! $postInfo) {
                    return self::checkInfo(self::POST_COMMENTS_POSTS_ERROR);
                }
                if ($postInfo['group_id']) {
                    $groupInfo = FresnsGroups::find($postInfo['group_id']);
                    if (! $groupInfo) {
                        return self::checkInfo(self::GROUP_EXISTS);
                    }
                    if ($groupInfo['type'] == 1) {
                        return self::checkInfo(self::GROUP_TYPE_ERROR);
                    }
                    $publishRule = FresnsGroupsService::publishRule($mid, $groupInfo['permission'], $groupInfo['id']);
                    if (! $publishRule['allowComment']) {
                        return self::checkInfo(self::POST_COMMENTS_ALLOW_ERROR);
                    }
                }
                if (empty($commentLog['content']) && (empty($commentLog['files_json']) || empty(json_decode($commentLog['files_json'], true))) && (empty($commentLog['extends_json']) || empty(json_decode($commentLog['extends_json'], true)))) {
                    return self::checkInfo(self::POSTS_LOG_CHECK_PARAMS_ERROR);
                }
                // Stop Word Rule Check
                if ($commentLog['content']) {
                    $message = self::stopWords($commentLog['content']);
                    if (! $message) {
                        return self::checkInfo(self::COMMENT_CONTENT_WORDS_ERROR);
                    }
                }

                break;
        }
    }

    /*
     * Fast Publishing Check
     */
    public static function checkPublish($mid)
    {
        $request = request();
        // In case of private mode, the feature cannot be requested when it expires (members > expired_at).
        $site_mode = ApiConfigHelper::getConfigByItemKey(AmConfig::SITE_MODEL);
        if ($site_mode == AmConfig::PRIVATE) {
            $memberInfo = FresnsMembers::find($mid);
            if ($memberInfo['expired_at'] && ($memberInfo['expired_at'] <= date('Y-m-d H:i:s'))) {
                return self::checkInfo(self::MEMBER_EXPIRED_LOGS_ERROR);
            }
        }
        $commentPid = $request->input('commentPid');
        $commentCid = $request->input('commentCid');
        $postGid = $request->input('postGid');
        $type = $request->input('type');
        if ($commentCid) {
            $commentInfo = FresnsComments::where('uuid', $commentCid)->first();
            if (! $commentInfo) {
                return self::checkInfo(self::COMMENT_LOGS_EXISTS);
            }
        }
        if ($commentPid) {
            // Post Info
            $postInfo = FresnsPosts::where('uuid', $commentPid)->first();
            if (! $postInfo) {
                return self::checkInfo(self::POST_LOGS_EXISTS);
            }
        }
        if ($type == 2) {
            // Get the maximum number of words in a comment
            $commentEditorWordCount = ApiConfigHelper::getConfigByItemKey(AmConfig::COMMENT_EDITOR_WORD_COUNT) ?? 1000;
            $content = $request->input('content');
            if (mb_strlen(trim($content)) > $commentEditorWordCount) {
                return self::checkInfo(self::CONTENT_COUNT_ERROR);
            }
            if (empty($commentPid)) {
                return self::checkInfo(self::COMMENT_PID_ERROR);
            }
            // Whether to have group publish privileges
            $postInfo = FresnsPosts::where('uuid', $commentPid)->first();
            if ($postInfo['group_id']) {
                $groupInfo = FresnsGroups::find($postInfo['group_id']);
                if (! $groupInfo) {
                    return self::checkInfo(self::GROUP_EXISTS);
                }
                if ($groupInfo['type'] == 1) {
                    return self::checkInfo(self::GROUP_TYPE_ERROR);
                }
                $publishRule = FresnsGroupsService::publishRule($mid, $groupInfo['permission'], $groupInfo['id']);
                if (! $publishRule['allowComment']) {
                    return self::checkInfo(self::POST_COMMENTS_ALLOW_ERROR);
                }
            }
        } else {
            // Get the maximum number of words in a post
            $postEditorWordCount = ApiConfigHelper::getConfigByItemKey(AmConfig::POST_EDITOR_WORD_COUNT) ?? 1000;
            $content = $request->input('content');
            if (mb_strlen(trim($content)) > $postEditorWordCount) {
                return self::checkInfo(self::CONTENT_COUNT_ERROR);
            }
            if ($postGid) {
                $groupInfo = FresnsGroups::where('uuid', $postGid)->where('is_enable', 1)->first();
                if (! $groupInfo) {
                    return self::checkInfo(self::GROUP_EXISTS);
                }
                if ($groupInfo['type'] == 1) {
                    return self::checkInfo(self::GROUP_TYPE_ERROR);
                }
                // Whether to have group publish privileges
                $publishRule = FresnsGroupsService::publishRule($mid, $groupInfo['permission'], $groupInfo['id']);
                if (! $publishRule['allowPost']) {
                    return self::checkInfo(self::POST_GROUP_ALLOW_ERROR);
                }
            }
        }
        // Stop Word Rule Check
        $message = self::stopWords($request->input('content'));
        if (! $message) {
            return self::checkInfo(self::COMMENT_CONTENT_WORDS_ERROR);
        }
        $file = $request->input('file');
        $fileInfo = $request->input('fileInfo');
        if ($fileInfo) {
            $filesJsonStatus = StrHelper::isJson($fileInfo);
            if (! $filesJsonStatus) {
                return self::checkInfo(self::FILE_JSON_ERROR);
            }
            $fileInfo = json_decode($fileInfo, true);
            if (count($fileInfo) == count($fileInfo, 1)) {
                return self::checkInfo(self::FILE_JSON_ERROR);
            }
        }
        if (! empty($file) && ! empty($fileInfo)) {
            return self::checkInfo(self::FILE_OR_FILEINFO_ERROR);
        }
        $eid = $request->input('eid');
        if ($eid) {
            $eidJsonStatus = StrHelper::isJson($eid);
            if (! $eidJsonStatus) {
                return self::checkInfo(self::EXTENDS_JSON_ERROR);
            }
            $extendsJson = json_decode($eid, true);
            foreach ($extendsJson as $e) {
                $extend = FresnsExtends::where('uuid', $e)->first();
                if (! $extend) {
                    return self::checkInfo(self::EXTENDS_UUID_ERROR);
                }
            }
        }
    }

    // Stop Word Rules
    public static function stopWords($text)
    {
        $stopWordsArr = FresnsStopWords::get()->toArray();

        foreach ($stopWordsArr as $v) {
            $str = strstr($text, $v['word']);
            // dd($str);
            if ($str != false) {
                if ($v['content_mode'] == 2) {
                    $text = str_replace($v['word'], $v['replace_word'], $text);

                    return $text;
                }
                if ($v['content_mode'] == 3) {
                    return false;
                }
                if ($v['content_mode'] == 4) {
                    return $v['content_mode'];
                }
            }
        }

        return $text;
    }

    // Calibrate the interface if it is in private mode, when the interface is not requestable after expiration (members > expired_at).
    public static function checkUploadPermission($memberId, $type, $fileSize = null, $suffix = null)
    {
        $siteMode = ApiConfigHelper::getConfigByItemKey('site_mode');
        $member = FresnsMembers::where('id', $memberId)->first();
        $time = date('Y-m-d H:i:s', time());
        if ($siteMode == 'private') {
            if (! empty($member['expired_at'])) {
                if ($time > $member['expired_at']) {
                    return ErrorCodeService::MEMBER_EXPIRED_AT_ERROR;
                }
            }
        }

        $roleId = FresnsMemberRoleRelsService::getMemberRoleRels($memberId);

        if (empty($roleId)) {
            return ErrorCodeService::USERS_NOT_AUTHORITY_ERROR;
        }

        $memberRole = FresnsMemberRoles::where('id', $roleId)->first();
        if (! empty($memberRole)) {
            $permission = $memberRole['permission'];
            $permissionArr = json_decode($permission, true);
            if (! empty($permissionArr)) {
                $permissionMap = FresnsMemberRolesService::getPermissionMap($permissionArr);
                if (empty($permissionMap)) {
                    return ErrorCodeService::USERS_NOT_AUTHORITY_ERROR;
                }
                $mbFileSize = null;
                if ($fileSize) {
                    $mbFileSize = $fileSize;
                }
                switch ($type) {
                    case 1:
                        // Verify member upload permissions (image)
                        if ($permissionMap['post_editor_image'] == false) {
                            return ErrorCodeService::USERS_NOT_AUTHORITY_ERROR;
                        }

                        if (! empty($mbFileSize)) {
                            // Check global configuration of uploadable image size
                            $images_max_size = ApiConfigHelper::getConfigByItemKey('images_max_size');
                            if ($mbFileSize > $images_max_size * 1024 * 1024) {
                                return ErrorCodeService::UPLOAD_FILES_SIZE_ERROR;
                            }
                            if ($mbFileSize > $permissionMap['images_max_size'] * 1024 * 1024) {
                                return ErrorCodeService::MEMBER_UPLOAD_FILES_SIZE_ERROR;
                            }
                        }

                        if (! empty($suffix)) {
                            $images_ext = ApiConfigHelper::getConfigByItemKey('images_ext');
                            $imagesExtArr = explode(',', $images_ext);
                            if (! in_array($suffix, $imagesExtArr)) {
                                return ErrorCodeService::UPLOAD_FILES_SUFFIX_ERROR;
                            }
                        }

                        break;
                    case 2:
                        // Verify member upload permissions (video)
                        if ($permissionMap['post_editor_video'] == false) {
                            return ErrorCodeService::USERS_NOT_AUTHORITY_ERROR;
                        }
                        if (! empty($mbFileSize)) {
                            // Check global configuration of uploadable video size
                            $videos_max_size = ApiConfigHelper::getConfigByItemKey('videos_max_size');
                            if ($mbFileSize > $videos_max_size * 1024 * 1024) {
                                return ErrorCodeService::UPLOAD_FILES_SIZE_ERROR;
                            }
                            if ($mbFileSize > $permissionMap['videos_max_size'] * 1024 * 1024) {
                                return ErrorCodeService::MEMBER_UPLOAD_FILES_SIZE_ERROR;
                            }
                        }
                        if (! empty($suffix)) {
                            $videos_ext = ApiConfigHelper::getConfigByItemKey('videos_ext');
                            $videosExtArr = explode(',', $videos_ext);
                            if (! in_array($suffix, $videosExtArr)) {
                                return ErrorCodeService::UPLOAD_FILES_SUFFIX_ERROR;
                            }
                        }

                        break;
                    case 3:
                        // Verify member upload permissions (audio)
                        if ($permissionMap['post_editor_audio'] == false) {
                            return ErrorCodeService::USERS_NOT_AUTHORITY_ERROR;
                        }
                        if (! empty($mbFileSize)) {
                            // Check global configuration of uploadable audio size
                            $audios_max_size = ApiConfigHelper::getConfigByItemKey('audios_max_size');
                            if ($mbFileSize > $audios_max_size * 1024 * 1024) {
                                return ErrorCodeService::UPLOAD_FILES_SIZE_ERROR;
                            }
                            if ($mbFileSize > $permissionMap['audios_max_size'] * 1024 * 1024) {
                                return ErrorCodeService::MEMBER_UPLOAD_FILES_SIZE_ERROR;
                            }
                        }

                        if (! empty($suffix)) {
                            $audios_ext = ApiConfigHelper::getConfigByItemKey('audios_ext');
                            $audiosExtArr = explode(',', $audios_ext);
                            if (! in_array($suffix, $audiosExtArr)) {
                                return ErrorCodeService::UPLOAD_FILES_SUFFIX_ERROR;
                            }
                        }

                        break;
                    default:
                        // Verify member upload permissions (doc)
                        if ($permissionMap['post_editor_doc'] == false) {
                            return ErrorCodeService::USERS_NOT_AUTHORITY_ERROR;
                        }
                        if (! empty($mbFileSize)) {
                            // Check global configuration of uploadable document size
                            $doc_max_size = ApiConfigHelper::getConfigByItemKey('docs_max_size');
                            if ($mbFileSize > $doc_max_size * 1024 * 1024) {
                                return ErrorCodeService::UPLOAD_FILES_SIZE_ERROR;
                            }
                            if ($mbFileSize > $permissionMap['docs_max_size'] * 1024 * 1024) {
                                return ErrorCodeService::MEMBER_UPLOAD_FILES_SIZE_ERROR;
                            }
                        }

                        if (! empty($suffix)) {
                            $docs_ext = ApiConfigHelper::getConfigByItemKey('docs_ext');
                            $docsExtArr = explode(',', $docs_ext);
                            if (! in_array($suffix, $docsExtArr)) {
                                return ErrorCodeService::UPLOAD_FILES_SUFFIX_ERROR;
                            }
                        }

                        break;
                }
            }
        }

        return true;
    }
}
