<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Editor;

use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\LogService;
use App\Http\FresnsApi\Helpers\ApiConfigHelper;
use App\Http\FresnsDb\FresnsCommentLogs\FresnsCommentLogs;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsMemberRoleRels\FresnsMemberRoleRelsService;
use App\Http\FresnsDb\FresnsMemberRoles\FresnsMemberRolesService;
use App\Http\FresnsDb\FresnsPostLogs\FresnsPostLogs;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use App\Http\FresnsDb\FresnsUsers\FresnsUsersConfig;
use Illuminate\Support\Facades\DB;

class FsService
{
    /**
     * post_logs > post_id and comment_logs > comment_id
     * https://fresns.cn/api/editor/delete.html.
     *
     * What's New content: empty, representing new content.
     * Edit existing content: With value, means edit content.
     */

    // Delete the entire log
    public function deletePostComment($uid, $mid, $logs, $type)
    {
        $deleteType = request()->input('deleteType');
        $deleteUuid = request()->input('deleteUuid');
        $filesJson = $logs['files_json'];
        $extendsJson = $logs['extends_json'];
        $filesArr = json_decode($filesJson, true);
        $extendArr = json_decode($extendsJson, true);
        switch ($deleteType) {
            case 1:
                // Journal with official content
                if ((empty($logs['post_id']) && $type == 1) || (empty($logs['comment_id']) && $type == 2)) {
                    // If the log contains attachments
                    if (! empty($filesArr)) {
                        if (! empty($filesArr)) {
                            $filesUuidArr = [];
                            foreach ($filesArr as $v) {
                                $filesUuidArr[] = $v['fid'];
                            }

                            if ($filesUuidArr) {
                                FresnsFiles::whereIn('uuid', $filesUuidArr)->delete();
                            }
                        }
                    }
                    if (! empty($extendArr)) {
                        // If this log contains extends content
                        $extendIdArr = [];
                        foreach ($extendArr as $v) {
                            $eid = FresnsExtends::where('uuid', $v['eid'])->value('id');
                            $count = DB::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('extend_id', $eid)->where('deleted_at', null)->count();

                            if ($count == 0) {
                                $extendIdArr[] = $eid;
                            } else {
                                return ErrorCodeService::DELETE_EXTEND_ERROR;
                            }
                        }
                        if ($extendIdArr) {
                            FresnsExtends::whereIn('id', $extendIdArr)->delete();
                        }
                    }
                    // Delete Log
                    if ($type == 1) {
                        FresnsPostLogs::where('id', $logs['id'])->delete();
                    } else {
                        FresnsCommentLogs::where('id', $logs['id'])->delete();
                    }
                } else {
                    // If the log contains attachments
                    if (! empty($filesArr)) {
                        $filesIdArr = [];
                        foreach ($filesArr as $v) {
                            $filesIdArr[] = $v['fid'];
                        }

                        if ($type == 1) {
                            $isCheck = $this->deletePostFiles($logs['id'], $filesIdArr, $logs['post_id']);
                            if ($isCheck !== true) {
                                return $isCheck;
                            }
                        } else {
                            $isCheck = $this->deleteCommentFiles($logs['id'], $filesIdArr, $logs['comment_id']);
                            if ($isCheck !== true) {
                                return $isCheck;
                            }
                        }
                        // If this log contains extends content
                        if (! empty($extendArr)) {
                            $extendIdArr = [];
                            foreach ($extendArr as $v) {
                                $eid = FresnsExtends::where('uuid', $v['eid'])->value('id');
                                $count = DB::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('extend_id', $eid)->where('deleted_at', null)->count();
                                if ($count == 0) {
                                    $extendIdArr[] = $eid;
                                } else {
                                    return ErrorCodeService::DELETE_EXTEND_ERROR;
                                }
                            }
                            if ($extendIdArr) {
                                FresnsExtends::whereIn('id', $extendIdArr)->delete();
                            }
                        }
                    }
                    // Delete Log
                    if ($type == 1) {
                        FresnsPostLogs::where('id', $logs['id'])->delete();
                    } else {
                        FresnsCommentLogs::where('id', $logs['id'])->delete();
                    }
                }
                break;
            case 2:
                // New log only
                if (empty($logs['post_id'])) {
                    FresnsFiles::where('uuid', $deleteUuid)->delete();
                } else {
                    if ($type == 1) {
                        $isCheck = $this->deletePostFiles($logs['id'], [$deleteUuid], $logs['post_id']);
                        if ($isCheck !== true) {
                            return $isCheck;
                        }
                    } else {
                        $isCheck = $this->deleteCommentFiles($logs['id'], [$deleteUuid], $logs['comment_id']);
                        if ($isCheck !== true) {
                            return $isCheck;
                        }
                    }
                }
                break;
            default:
                // Query whether extend_linkeds has content associated with this extension
                $eid = FresnsExtends::where('uuid', $deleteUuid)->value('id');
                $count = DB::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('deleted_at', null)->where('extend_id', $eid)->count();
                if ($count == 0) {
                    FresnsExtends::where('id', $eid)->delete();
                } else {
                    return ErrorCodeService::DELETE_EXTEND_ERROR;
                }

                break;
        }

        return true;
    }

    // Delete post attachment
    public function deletePostFiles($logId, $filesIdArr, $postId)
    {
        // Flow 1
        $postsMoreJson = FresnsPosts::where('more_json', 'LIKE', '%files%')->pluck('more_json')->toArray();
        $postFidIdArr = [];
        if ($postsMoreJson) {
            foreach ($postsMoreJson as $v) {
                $jsonArr = json_decode($v, true);
                if (empty($jsonArr['files'])) {
                    continue;
                }
                foreach ($jsonArr['files'] as $file) {
                    if (empty($file['fid'])) {
                        continue;
                    }
                    $postFidIdArr[] = $file['fid'];
                }
            }
        }
        $fidArr = [];
        if (! empty($postFidIdArr)) {
            foreach ($filesIdArr as $v) {
                if (in_array($v, $postFidIdArr)) {
                    // Process terminated if used
                    return ErrorCodeService::DELETE_FILE_ERROR;
                }
            }
        }

        // Flow 2
        $postsLogsFilesJson = FresnsPostLogs::where('files_json', '!=', null)->where('id', '!=', $logId)->where('state', 3)->pluck('files_json')->toArray();
        if (! empty($postsLogsFilesJson)) {
            $postLogFidArr = [];
            foreach ($postsLogsFilesJson as $v) {
                $postsLogsFilesArr = json_decode($v, true);
                foreach ($postsLogsFilesArr as $file) {
                    if (empty($file['fid'])) {
                        continue;
                    }
                    $postLogFidArr[] = $file['fid'];
                }
            }
            if (! empty($postLogFidArr)) {
                foreach ($filesIdArr as $v) {
                    if (in_array($v, $postLogFidArr)) {
                        // Process terminated if used
                        return ErrorCodeService::DELETE_FILE_ERROR;
                    }
                }
            }
        }

        // Flow 3
        FresnsFiles::whereIn('uuid', $filesIdArr)->delete();

        return true;
    }

    // Delete comment attachment
    public function deleteCommentFiles($logId, $filesIdArr, $commentId)
    {
        // Flow 1
        $commentMoreJson = FresnsComments::where('more_json', 'LIKE', '%files%')->pluck('more_json')->toArray();
        $commentFidIdArr = [];
        if ($commentMoreJson) {
            foreach ($commentMoreJson as $v) {
                $jsonArr = json_decode($v, true);
                if (empty($jsonArr['files'])) {
                    continue;
                }
                foreach ($jsonArr['files'] as $file) {
                    if (empty($file['fid'])) {
                        continue;
                    }
                    $commentFidIdArr[] = $file['fid'];
                }
            }
        }
        $fidArr = [];
        if (! empty($commentFidIdArr)) {
            foreach ($filesIdArr as $v) {
                if (in_array($v, $commentFidIdArr)) {
                    // Process terminated if used
                    return ErrorCodeService::DELETE_FILE_ERROR;
                }
            }
        }

        // Flow 2
        $commentLogsFilesJson = FresnsCommentLogs::where('files_json', '!=', null)->where('id', '!=', $logId)->where('state', 3)->pluck('files_json')->toArray();
        if (! empty($commentLogsFilesJson)) {
            $commentLogFidArr = [];
            foreach ($commentLogsFilesJson as $v) {
                $commentLogsFilesArr = json_decode($v, true);
                foreach ($commentLogsFilesArr as $file) {
                    if (empty($file['fid'])) {
                        continue;
                    }
                    $commentLogFidArr[] = $file['fid'];
                }
            }
            if (! empty($commentLogFidArr)) {
                foreach ($filesIdArr as $v) {
                    if (in_array($v, $commentLogFidArr)) {
                        // Process terminated if used
                        return ErrorCodeService::DELETE_FILE_ERROR;
                    }
                }
            }
        }

        // Flow 3
        FresnsFiles::whereIn('uuid', $filesIdArr)->delete();

        return true;
    }

    // Editor config info: Publish post perm
    public function publishPostPerm($user, $permission)
    {
        // Global checksum (post)
        // Email, Phone number, Real name
        $post_email_verify = ApiConfigHelper::getConfigByItemKey('post_email_verify');
        if ($post_email_verify == true) {
            if (empty($user->email)) {
                return ErrorCodeService::PUBLISH_EMAIL_VERIFY_ERROR;
            }
        }
        $post_phone_verify = ApiConfigHelper::getConfigByItemKey('post_phone_verify');
        if ($post_phone_verify == true) {
            if (empty($user->phone)) {
                return ErrorCodeService::PUBLISH_PHONE_VERIFY_ERROR;
            }
        }
        $post_prove_verify = ApiConfigHelper::getConfigByItemKey('post_prove_verify');
        if ($post_prove_verify == true) {
            if ($user->prove_verify == 1) {
                return ErrorCodeService::PUBLISH_PROVE_VERIFY_ERROR;
            }
        }
        if ($permission) {
            $permissionArr = json_decode($permission, true);
            if ($permissionArr) {
                $permissionMap = FresnsMemberRolesService::getPermissionMap($permissionArr);
                LogService::info('permissionMap-checkPermission', $permissionMap);
                if ($permissionMap['post_publish'] == false) {
                    return ErrorCodeService::ROLE_NO_PERMISSION_PUBLISH;
                }
                // Publish Post Request - Email
                if ($permissionMap['post_email_verify'] == true) {
                    if (empty($user->email)) {
                        return ErrorCodeService::ROLE_PUBLISH_EMAIL_VERIFY;
                    }
                }
                // Publish Post Request - Phone Number
                if ($permissionMap['post_phone_verify'] == true) {
                    if (empty($user->phone)) {
                        return ErrorCodeService::ROLE_PUBLISH_PHONE_VERIFY;
                    }
                }
                // Publish Post Request - Real name
                if ($permissionMap['post_prove_verify'] == true) {
                    if ($user->prove_verify == 1) {
                        return ErrorCodeService::ROLE_PUBLISH_PROVE_VERIFY;
                    }
                }
            }
        }

        return 0;
    }

    // Editor config info: Role limit permissions
    public function postRoleLimit($permissionMap)
    {
        if ($permissionMap['post_limit_status'] == true) {
            $post_limit_rule = $permissionMap['post_limit_rule'];
            if ($permissionMap['post_limit_type'] == 1) {
                $post_limit_period_start = $permissionMap['post_limit_period_start'];
                $post_limit_period_end = $permissionMap['post_limit_period_end'];
                $time = date('Y-m-d H:i:s', time());
                if ($post_limit_rule == 2) {
                    if ($post_limit_period_start <= $time && $post_limit_period_end >= $time) {
                        return true;
                    }
                } else {
                    if ($post_limit_period_start > $time || $post_limit_period_end < $time) {
                        return true;
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
                    $post_limit_cycle_end = date('Y-m-d', strtotime('+1 day')).' '.$post_limit_cycle_end;
                }
                $time = date('Y-m-d H:i:s', time());
                if ($post_limit_rule == 2) {
                    if ($post_limit_cycle_start <= $time && $post_limit_cycle_end >= $time) {
                        return true;
                    }
                } else {
                    if ($time < $post_limit_cycle_start || $time > $post_limit_cycle_end) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    // Editor config info: Global limit permissions
    public function postGlobalLimit($roleId)
    {
        $post_limit_status = ApiConfigHelper::getConfigByItemKey('post_limit_status');

        if ($post_limit_status === true) {
            if (! empty($roleId)) {
                // Get a list of whitelisted roles
                $post_limit_whitelist = ApiConfigHelper::getConfigByItemKey('post_limit_whitelist');
                if (! empty($post_limit_whitelist)) {
                    $post_limit_whitelist_arr = json_decode($post_limit_whitelist, true);
                    if (in_array($roleId, $post_limit_whitelist_arr)) {
                        return false;
                    }
                }
            }
        }

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
                        return true;
                    }
                } else {
                    if ($post_limit_period_start > $time || $post_limit_period_end < $time) {
                        return true;
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
                    $post_limit_cycle_end = date('Y-m-d', strtotime('+1 day')).' '.$post_limit_cycle_end;
                }
                $time = date('Y-m-d H:i:s', time());
                if ($post_limit_rule == 2) {
                    if ($post_limit_cycle_start <= $time && $post_limit_cycle_end >= $time) {
                        return true;
                    }
                } else {
                    if ($time < $post_limit_cycle_start || $time > $post_limit_cycle_end) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    // Editor config info: Publish comment perm
    public function publishCommentPerm($user, $permission)
    {
        // Publish Comment Request - Email
        $comment_email_verify = ApiConfigHelper::getConfigByItemKey('comment_email_verify');
        if ($comment_email_verify == true) {
            if (empty($user->email)) {
                return ErrorCodeService::PUBLISH_EMAIL_VERIFY_ERROR;
            }
        }
        // Publish Comment Request - Phone Number
        $comment_phone_verify = ApiConfigHelper::getConfigByItemKey('comment_phone_verify');
        if ($comment_phone_verify == true) {
            if (empty($user->phone)) {
                return ErrorCodeService::PUBLISH_PHONE_VERIFY_ERROR;
            }
        }
        // Publish Comment Request - Real name
        $comment_prove_verify = ApiConfigHelper::getConfigByItemKey('comment_prove_verify');
        if ($comment_prove_verify == true) {
            if ($user->prove_verify == 1) {
                return ErrorCodeService::PUBLISH_PROVE_VERIFY_ERROR;
            }
        }
        if ($permission) {
            $permissionArr = json_decode($permission, true);
            $permissionMap = FresnsMemberRolesService::getPermissionMap($permissionArr);
            // Publish Comment Permissions
            if ($permissionMap['comment_publish'] == false) {
                return ErrorCodeService::ROLE_NO_PERMISSION_PUBLISH;
            }
            // Publish Comment Request - Email
            if ($permissionMap['comment_email_verify'] == true) {
                if (empty($user->email)) {
                    return ErrorCodeService::ROLE_PUBLISH_EMAIL_VERIFY;
                }
            }
            // Publish Comment Request - Phone Number
            if ($permissionMap['comment_phone_verify'] == true) {
                if (empty($user->phone)) {
                    return ErrorCodeService::ROLE_PUBLISH_PHONE_VERIFY;
                }
            }
            // Publish Comment Request - Real name
            if ($permissionMap['comment_prove_verify'] == true) {
                if ($user->prove_verify == 1) {
                    return ErrorCodeService::ROLE_PUBLISH_PROVE_VERIFY;
                }
            }
        }

        return 0;
    }

    // Editor config info: Role limit perm
    public function commentRoleLimit($permissionMap)
    {
        if ($permissionMap['comment_limit_status'] == true) {
            $comment_limit_rule = $permissionMap['comment_limit_rule'];
            $comment_limit_type = $permissionMap['comment_limit_type'];
            // 1.All-day limit on specified dates
            if ($comment_limit_type == 1) {
                $comment_limit_period_start = $permissionMap['comment_limit_period_start'];
                $comment_limit_period_end = $permissionMap['comment_limit_period_end'];
                $time = date('Y-m-d H:i:s', time());
                if ($comment_limit_rule == 2) {
                    if ($comment_limit_period_start <= $time && $comment_limit_period_end >= $time) {
                        return true;
                    }
                } else {
                    if ($time < $comment_limit_period_start || $time > $comment_limit_period_end) {
                        return true;
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
                    $post_limit_cycle_end = date('Y-m-d', strtotime('+1 day')).' '.$comment_limit_cycle_end;
                }
                $time = date('Y-m-d H:i:s', time());
                if ($comment_limit_rule == 2) {
                    if ($comment_limit_cycle_start <= $time && $comment_limit_cycle_end >= $time) {
                        return true;
                    }
                } else {
                    if ($time < $comment_limit_cycle_start || $time > $comment_limit_cycle_end) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    // Editor config info: Global limit perm
    public function commentGlobalLimit($roleId)
    {
        $comment_limit_status = ApiConfigHelper::getConfigByItemKey('comment_limit_status');
        // If the member master role is a whitelisted role, it is not subject to this permission requirement
        if ($comment_limit_status == true) {
            if (! empty($roleId)) {
                // Get a list of whitelisted roles
                $comment_limit_whitelist = ApiConfigHelper::getConfigByItemKey('comment_limit_whitelist');
                if (! empty($comment_limit_whitelist)) {
                    $comment_limit_whitelist_arr = json_decode($comment_limit_whitelist, true);
                    if (in_array($roleId, $comment_limit_whitelist_arr)) {
                        return false;
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
                        return true;
                    }
                } else {
                    if ($time < $comment_limit_period_start || $time > $comment_limit_period_end) {
                        return true;
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
                    $post_limit_cycle_end = date('Y-m-d', strtotime('+1 day')).' '.$comment_limit_cycle_end;
                }
                $time = date('Y-m-d H:i:s', time());
                if ($comment_limit_rule == 2) {
                    if ($comment_limit_cycle_start <= $time && $comment_limit_cycle_end >= $time) {
                        return true;
                    }
                } else {
                    if ($time < $comment_limit_cycle_start || $time > $comment_limit_cycle_end) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
