<?php

/*
 * Fresns
 * Copyright (C) 2021-Present 唐杰
 * Released under the Apache-2.0 License.
 */

namespace App\Http\Fresns\FresnsApi\Editor;

use App\Http\Fresns\FresnsCommentLogs\FresnsCommentLogs;
use App\Http\Fresns\FresnsComments\FresnsComments;
use App\Http\Fresns\FresnsExtendLinkeds\FresnsExtendLinkedsConfig;
use App\Http\Fresns\FresnsExtends\FresnsExtends;
use App\Http\Fresns\FresnsFiles\FresnsFiles;
use App\Http\Fresns\FresnsPostLogs\FresnsPostLogs;
use App\Http\Fresns\FresnsPosts\FresnsPosts;
use App\Http\Share\Common\ErrorCodeService;
use App\Http\Share\Common\LogService;
use Illuminate\Support\Facades\DB;

class AmService
{
    /**
     * 新内容：post_logs > post_id 和 comment_logs > comment_id 为空，代表新内容
     * 编辑已有内容：post_logs > post_id 和 comment_logs > comment_id 有值，代表编辑内容.
     */
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
                //情况 1，新草稿：日志表 post_logs > post_id 或 comment_logs > comment_id 字段值为空的记录
                if ((empty($logs['post_id']) && $type == 1) || (empty($logs['comment_id']) && $type == 2)) {
                    //如果该草稿含有附属文件，则在该文件 files > deleted_at 字段填入时间。
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
                        //如果该草稿含有附属扩展内容，查询 extend_linkeds 是否有内容关联了该扩展内容；有则中止操作，没有则在 extends > deleted_at 字段填入时间。
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
                    //最后在 post_logs > deleted_at 或 comment_logs > deleted_at 字段填入时间。
                    if ($type == 1) {
                        FresnsPostLogs::where('id', $logs['id'])->delete();
                    } else {
                        FresnsCommentLogs::where('id', $logs['id'])->delete();
                    }
                } else {
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
                        if (! empty($extendArr)) {
                            //如果该草稿含有附属扩展内容，查询 extend_linkeds 是否有内容关联了该扩展内容；有则中止操作，没有则在 extends > deleted_at 字段填入时间。
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
                    //最后在 post_logs > deleted_at 或 comment_logs > deleted_at 字段填入时间。
                    if ($type == 1) {
                        FresnsPostLogs::where('id', $logs['id'])->delete();
                    } else {
                        FresnsCommentLogs::where('id', $logs['id'])->delete();
                    }
                }
                break;
            case 2:
                //情况 1：该文件所属日志为新草稿，仅在 files > deleted_at 字段填入时间。
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
                //查询 extend_linkeds 是否有内容关联了该扩展内容；有则中止操作（还有其他内容在使用该扩展，包括自己主表的关联也算），没有则在 extends > deleted_at 字段填入时间。
                $eid = FresnsExtends::where('uuid', $deleteUuid)->value('id');
                $count = DB::table(FresnsExtendLinkedsConfig::CFG_TABLE)->where('deleted_at', null)->where('extend_id',
                    $eid)->count();
                if ($count == 0) {
                    FresnsExtends::where('id', $eid)->delete();
                } else {
                    return ErrorCodeService::DELETE_EXTEND_ERROR;
                }

                break;
        }

        return true;
    }

    //删除帖子相关文件
    public function deletePostFiles($logId, $filesIdArr, $postId)
    {
        //流程 1、查询主表是否在使用 posts > more_json > files，在使用则流程中止，没有使用则继续下一个流程。
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
                    //如果有使用则流程终止
                    return ErrorCodeService::DELETE_FILES_ERROR;
                }
            }
        }

        //流程2 ：查询其他 post_logs > status = 3 的日志是否在使用 post_logs > files_json，在使用则流程中止，没有使用则继续下一个流程。
        $postsLogsFilesJson = FresnsPostLogs::where('files_json', '!=', null)->where('id', '!=',
            $logId)->where('status', 3)->pluck('files_json')->toArray();

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
                        //如果有使用则流程终止
                        return ErrorCodeService::DELETE_FILES_ERROR;
                    }
                }
            }
        }

        //在 files > deleted_at 字段填入时间。
        FresnsFiles::whereIn('uuid', $filesIdArr)->delete();

        return true;
    }

    //删除评论相关
    public function deleteCommentFiles($logId, $filesIdArr, $commentId)
    {
        //流程 流程 1、查询主表是否在使用 comments > more_json > files，在使用则流程中止，没有使用则继续下一个流程。
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
                    //如果有使用则流程终止
                    return ErrorCodeService::DELETE_FILES_ERROR;
                }
            }
        }
        //如果有未使用的

        $commentLogsFilesJson = FresnsCommentLogs::where('files_json', '!=', null)->where('id', '!=',
            $logId)->where('status', 3)->pluck('files_json')->toArray();
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
                        //如果有使用则流程终止
                        return ErrorCodeService::DELETE_FILES_ERROR;
                    }
                }
            }
        }
        //在 files > deleted_at 字段填入时间。
        FresnsFiles::whereIn('uuid', $filesIdArr)->delete();

        return true;
    }
}
