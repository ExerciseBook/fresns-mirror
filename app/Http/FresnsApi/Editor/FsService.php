<?php

/*
 * Fresns (https://fresns.cn)
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\FresnsApi\Editor;

use App\Http\Center\Common\ErrorCodeService;
use App\Http\Center\Common\LogService;
use App\Http\FresnsDb\FresnsCommentLogs\FresnsCommentLogs;
use App\Http\FresnsDb\FresnsComments\FresnsComments;
use App\Http\FresnsDb\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use App\Http\FresnsDb\FresnsExtends\FresnsExtends;
use App\Http\FresnsDb\FresnsFiles\FresnsFiles;
use App\Http\FresnsDb\FresnsPostLogs\FresnsPostLogs;
use App\Http\FresnsDb\FresnsPosts\FresnsPosts;
use Illuminate\Support\Facades\DB;

class AmService
{
    /**
     * post_logs > post_id and comment_logs > comment_id
     * https://fresns.org/api/editor/delete.html
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
                    return ErrorCodeService::DELETE_FILES_ERROR;
                }
            }
        }

        // Flow 2
        $postsLogsFilesJson = FresnsPostLogs::where('files_json', '!=', null)->where('id', '!=', $logId)->where('status', 3)->pluck('files_json')->toArray();
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
                        return ErrorCodeService::DELETE_FILES_ERROR;
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
                    return ErrorCodeService::DELETE_FILES_ERROR;
                }
            }
        }

        // Flow 2
        $commentLogsFilesJson = FresnsCommentLogs::where('files_json', '!=', null)->where('id', '!=', $logId)->where('status', 3)->pluck('files_json')->toArray();
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
                        return ErrorCodeService::DELETE_FILES_ERROR;
                    }
                }
            }
        }

        // Flow 3
        FresnsFiles::whereIn('uuid', $filesIdArr)->delete();

        return true;
    }
}
