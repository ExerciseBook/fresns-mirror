<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Content;

use App\Fresns\Words\Content\DTO\CreateDraftDTO;
use App\Fresns\Words\Content\DTO\GenerateDraftDTO;
use App\Fresns\Words\Content\DTO\LogicalDeletionContentDTO;
use App\Fresns\Words\Content\DTO\PhysicalDeletionContentDTO;
use App\Fresns\Words\Content\DTO\ReleaseContentDTO;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\ArchiveUsage;
use App\Models\Comment;
use App\Models\CommentAppend;
use App\Models\CommentLog;
use App\Models\DomainLinkUsage;
use App\Models\ExtendUsage;
use App\Models\File;
use App\Models\FileUsage;
use App\Models\HashtagUsage;
use App\Models\Language;
use App\Models\Mention;
use App\Models\OperationUsage;
use App\Models\Post;
use App\Models\PostAllow;
use App\Models\PostAppend;
use App\Models\PostLog;
use App\Models\PostUser;
use App\Utilities\ConfigUtility;
use App\Utilities\ContentUtility;
use App\Utilities\InteractiveUtility;
use App\Utilities\PermissionUtility;
use Carbon\Carbon;
use Fresns\CmdWordManager\Traits\CmdWordResponseTrait;
use Illuminate\Support\Str;

class Content
{
    use CmdWordResponseTrait;

    // createDraft
    public function createDraft($wordBody)
    {
        $dtoWordBody = new CreateDraftDTO($wordBody);

        $userId = PrimaryHelper::fresnsUserIdByUidOrUsername($dtoWordBody->uid);

        $langTag = \request()->header('langTag', ConfigHelper::fresnsConfigDefaultLangTag());

        $isPluginEditor = 0;
        $editorUnikey = null;
        if ($dtoWordBody->editorUnikey) {
            $isPluginEditor = 1;
            $editorUnikey = $dtoWordBody->editorUnikey;
        }

        $content = null;
        if ($dtoWordBody->content) {
            $content = Str::of($dtoWordBody->content)->trim();
        }
        $isMarkdown = $dtoWordBody->isMarkdown ?? 0;
        $isAnonymous = $dtoWordBody->isAnonymous ?? 0;

        switch ($dtoWordBody->type) {
            // post
            case 1:
                $groupId = PrimaryHelper::fresnsGroupIdByGid($dtoWordBody->gid);

                $title = null;
                if ($dtoWordBody->postTitle) {
                    $title = Str::of($dtoWordBody->postTitle)->trim();
                }

                $checkLog = PostLog::with(['files', 'extends'])->where('user_id', $userId)->where('create_type', 1)->where('state', 1)->first();

                $logData = [
                    'user_id' => $userId,
                    'create_type' => $dtoWordBody->createType,
                    'is_plugin_editor' => $isPluginEditor,
                    'editor_unikey' => $editorUnikey,
                    'group_id' => $groupId,
                    'title' => $title,
                    'content' => $content,
                    'is_markdown' => $isMarkdown,
                    'is_anonymous' => $isAnonymous,
                    'map_json' => $dtoWordBody->mapJson ?? null,
                ];

                if (! $checkLog) {
                    $logModel = PostLog::createMany($logData);
                }

                if (! $checkLog->content && ! $checkLog->files && ! $checkLog->extends) {
                    $logModel = $checkLog->update($logData);
                } else {
                    $logModel = PostLog::createMany($logData);
                }
            break;

            // comment
            case 2:
                $postId = PrimaryHelper::fresnsPostIdByPid($dtoWordBody->pid);

                if (empty($postId)) {
                    return $this->failure(
                        37300,
                        ConfigUtility::getCodeMessage(37300, 'Fresns', $langTag)
                    );
                }

                $checkLog = CommentLog::with(['files', 'extends'])->where('user_id', $userId)->where('create_type', 1)->where('state', 1)->first();

                $logData = [
                    'user_id' => $userId,
                    'create_type' => $dtoWordBody->createType,
                    'is_plugin_editor' => $isPluginEditor,
                    'editor_unikey' => $editorUnikey,
                    'content' => $content,
                    'is_markdown' => $isMarkdown,
                    'is_anonymous' => $isAnonymous,
                    'map_json' => $dtoWordBody->mapJson ?? null,
                ];

                if (! $checkLog) {
                    $logModel = CommentLog::createMany($logData);
                }

                if (! $checkLog->content && ! $checkLog->files && ! $checkLog->extends) {
                    $logModel = $checkLog->update($logData);
                } else {
                    $logModel = CommentLog::createMany($logData);
                }
            break;
        }

        if ($dtoWordBody->eid) {
            $extendId = PrimaryHelper::fresnsExtendIdByEid($dtoWordBody->eid);

            if ($extendId) {
                $usageType = match ($dtoWordBody->type) {
                    1 => ExtendUsage::TYPE_POST_LOG,
                    2 => ExtendUsage::TYPE_COMMENT_LOG,
                };

                ExtendUsage::createMany([
                    'usage_type' => $usageType,
                    'usage_id' => $logModel->id,
                    'extend_id' => $extendId,
                    'plugin_unikey' => 'Fresns',
                ]);
            }
        }

        return $this->success([
            'type' => $dtoWordBody->type,
            'logId' => $logModel->id,
        ]);
    }

    // generateDraft
    public function generateDraft($wordBody)
    {
        $dtoWordBody = new GenerateDraftDTO($wordBody);

        $langTag = \request()->header('langTag', ConfigHelper::fresnsConfigDefaultLangTag());
        $timezone = \request()->header('timezone', ConfigHelper::fresnsConfigDefaultTimezone());

        switch ($dtoWordBody->type) {
            // post
            case 1:
                $post = PrimaryHelper::fresnsModelByFsid('post', $dtoWordBody->fsid);
                $editConfig = ConfigHelper::fresnsConfigByItemKeys([
                    'post_edit',
                    'post_edit_time_limit',
                    'post_edit_sticky_limit',
                    'post_edit_digest_limit',
                ]);

                if (! $editConfig['post_edit']) {
                    return $this->failure(
                        36305,
                        ConfigUtility::getCodeMessage(36305, 'Fresns', $langTag)
                    );
                }

                $timeDiff = Carbon::parse($post->created_at)->diffInMinutes(now());

                if ($timeDiff > $editConfig['post_edit_time_limit']) {
                    return $this->failure(
                        36309,
                        ConfigUtility::getCodeMessage(36309, 'Fresns', $langTag)
                    );
                }

                if (! $editConfig['post_edit_sticky_limit'] && $post->sticky_state != 1) {
                    return $this->failure(
                        36307,
                        ConfigUtility::getCodeMessage(36307, 'Fresns', $langTag)
                    );
                }

                if (! $editConfig['post_edit_digest_limit'] && $post->digest_state != 1) {
                    return $this->failure(
                        36308,
                        ConfigUtility::getCodeMessage(36308, 'Fresns', $langTag)
                    );
                }

                $checkContentEditPerm = PermissionUtility::checkContentEditPerm($post->created_at, $editConfig['post_edit_time_limit'], $timezone, $langTag);
                $editableStatus = $checkContentEditPerm['editableStatus'];
                $editableTime = $checkContentEditPerm['editableTime'];
                $deadlineTime = $checkContentEditPerm['deadlineTime'];

                $logModel = ContentUtility::generatePostDraft($post);
            break;

            // comment
            case 2:
                $comment = PrimaryHelper::fresnsModelByFsid('comment', $dtoWordBody->fsid);

                if (! empty($comment->top_comment_id) || $comment->top_comment_id == 0) {
                    return $this->failure(
                        36313,
                        ConfigUtility::getCodeMessage(36313, 'Fresns', $langTag)
                    );
                }

                $editConfig = ConfigHelper::fresnsConfigByItemKeys([
                    'comment_edit',
                    'comment_edit_time_limit',
                    'comment_edit_sticky_limit',
                    'comment_edit_digest_limit',
                ]);

                if (! $editConfig['comment_edit']) {
                    return $this->failure(
                        36306,
                        ConfigUtility::getCodeMessage(36306, 'Fresns', $langTag)
                    );
                }

                $timeDiff = Carbon::parse($comment->created_at)->diffInMinutes(now());

                if ($timeDiff > $editConfig['comment_edit_time_limit']) {
                    return $this->failure(
                        36309,
                        ConfigUtility::getCodeMessage(36309, 'Fresns', $langTag)
                    );
                }

                if (! $editConfig['comment_edit_sticky_limit'] && $comment->sticky_state != 1) {
                    return $this->failure(
                        36307,
                        ConfigUtility::getCodeMessage(36307, 'Fresns', $langTag)
                    );
                }

                if (! $editConfig['comment_edit_digest_limit'] && $comment->digest_state != 1) {
                    return $this->failure(
                        36308,
                        ConfigUtility::getCodeMessage(36308, 'Fresns', $langTag)
                    );
                }

                $checkContentEditPerm = PermissionUtility::checkContentEditPerm($comment->created_at, $editConfig['comment_edit_time_limit'], $timezone, $langTag);
                $editableStatus = $checkContentEditPerm['editableStatus'];
                $editableTime = $checkContentEditPerm['editableTime'];
                $deadlineTime = $checkContentEditPerm['deadlineTime'];

                $logModel = ContentUtility::generateCommentDraft($comment);
            break;
        }

        return $this->success([
            'type' => $dtoWordBody->type,
            'logId' => $logModel->id,
            'editableStatus' => $editableStatus,
            'editableTime' => $editableTime,
            'deadlineTime' => $deadlineTime,
        ]);
    }

    // releaseContent
    public function releaseContent($wordBody)
    {
        $dtoWordBody = new ReleaseContentDTO($wordBody);

        $logModel = match ($dtoWordBody->type) {
            1 => PostLog::where('id', $dtoWordBody->logId)->first(),
            2 => CommentLog::where('id', $dtoWordBody->logId)->first(),
        };

        $langTag = \request()->header('langTag', ConfigHelper::fresnsConfigDefaultLangTag());

        if (empty($logModel)) {
            return $this->failure([
                38100,
                ConfigUtility::getCodeMessage(38100, 'Fresns', $langTag)
            ]);
        }

        if ($logModel->state == 2) {
            return $this->failure([
                38103,
                ConfigUtility::getCodeMessage(38103, 'Fresns', $langTag)
            ]);
        }

        if ($logModel->state == 3) {
            return $this->failure([
                38104,
                ConfigUtility::getCodeMessage(38104, 'Fresns', $langTag)
            ]);
        }

        switch ($dtoWordBody->type) {
            // post
            case 1:
                $post = ContentUtility::releasePost($logModel);

                $primaryId = $post->id;
                $fsid = $post->pid;
            break;

            // comment
            case 2:
                $comment = ContentUtility::releaseComment($logModel);

                $primaryId = $comment->id;
                $fsid = $comment->pid;
            break;
        }

        return $this->success([
            'type' => $dtoWordBody->type,
            'id' => $primaryId,
            'fsid' => $fsid,
        ]);
    }

    // logicalDeletionContent
    public function logicalDeletionContent($wordBody)
    {
        $dtoWordBody = new LogicalDeletionContentDTO($wordBody);

        switch ($dtoWordBody->contentType) {
            // main
            case 1:
                $model = match ($dtoWordBody->type) {
                    1 => Post::where('pid', $dtoWordBody->contentFsid)->first(),
                    2 => Comment::where('cid', $dtoWordBody->contentFsid)->first(),
                };

                $modelAppend = match ($dtoWordBody->type) {
                    1 => PostAppend::where('post_id', $model->id)->first(),
                    2 => CommentAppend::where('comment_id', $model->id)->first(),
                };

                $type = match ($dtoWordBody->type) {
                    1 => 'post',
                    2 => 'comment',
                };

                InteractiveUtility::publishStats($type, $model->id, 'decrement');

                if ($dtoWordBody->type == 1) {
                    PostAllow::where('post_id', $model->id)->delete();
                    PostUser::where('post_id', $model->id)->delete();
                    Language::where('table_name', 'post_appends')->where('table_column', 'allow_btn_name')->where('table_id', $model->id)->delete();
                    Language::where('table_name', 'post_appends')->where('table_column', 'user_list_name')->where('table_id', $model->id)->delete();
                    Language::where('table_name', 'post_appends')->where('table_column', 'comment_btn_name')->where('table_id', $model->id)->delete();
                }

                $tableName = match ($dtoWordBody->type) {
                    1 => 'posts',
                    2 => 'comments',
                };

                $usageType = match ($dtoWordBody->type) {
                    1 => OperationUsage::TYPE_POST,
                    2 => OperationUsage::TYPE_COMMENT,
                };
            break;

            // log
            case 2:
                $model = match ($dtoWordBody->type) {
                    1 => PostLog::where('id', $dtoWordBody->contentLogId)->first(),
                    2 => CommentLog::where('id', $dtoWordBody->contentLogId)->first(),
                };

                $tableName = match ($dtoWordBody->type) {
                    1 => 'post_logs',
                    2 => 'comment_logs',
                };

                $usageType = match ($dtoWordBody->type) {
                    1 => OperationUsage::TYPE_POST_LOG,
                    2 => OperationUsage::TYPE_COMMENT_LOG,
                };
            break;
        }

        FileUsage::where('table_name', $tableName)->where('table_column', 'id')->where('table_id', $model->id)->delete();
        OperationUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->delete();
        ArchiveUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->delete();
        ExtendUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->delete();

        HashtagUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->delete();
        DomainLinkUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->delete();
        Mention::where('user_id', $model->user_id)->where('mention_type', $usageType)->where('mention_id', $model->id)->delete();

        $modelAppend->delete();
        $model->delete();

        return $this->success();
    }

    // physicalDeletionContent
    public function physicalDeletionContent($wordBody)
    {
        $dtoWordBody = new PhysicalDeletionContentDTO($wordBody);

        switch ($dtoWordBody->contentType) {
            // main
            case 1:
                $model = match ($dtoWordBody->type) {
                    1 => Post::where('pid', $dtoWordBody->contentFsid)->first(),
                    2 => Comment::where('cid', $dtoWordBody->contentFsid)->first(),
                };

                $modelAppend = match ($dtoWordBody->type) {
                    1 => PostAppend::where('post_id', $model->id)->first(),
                    2 => CommentAppend::where('comment_id', $model->id)->first(),
                };

                $type = match ($dtoWordBody->type) {
                    1 => 'post',
                    2 => 'comment',
                };

                InteractiveUtility::publishStats($type, $model->id, 'decrement');

                if ($dtoWordBody->type == 1) {
                    PostAllow::where('post_id', $model->id)->forceDelete();
                    PostUser::where('post_id', $model->id)->forceDelete();
                    Language::where('table_name', 'post_appends')->where('table_column', 'allow_btn_name')->where('table_id', $model->id)->forceDelete();
                    Language::where('table_name', 'post_appends')->where('table_column', 'user_list_name')->where('table_id', $model->id)->forceDelete();
                    Language::where('table_name', 'post_appends')->where('table_column', 'comment_btn_name')->where('table_id', $model->id)->forceDelete();
                }

                $tableName = match ($dtoWordBody->type) {
                    1 => 'posts',
                    2 => 'comments',
                };

                $usageType = match ($dtoWordBody->type) {
                    1 => OperationUsage::TYPE_POST,
                    2 => OperationUsage::TYPE_COMMENT,
                };
            break;

            // log
            case 2:
                $model = match ($dtoWordBody->type) {
                    1 => PostLog::where('id', $dtoWordBody->contentLogId)->first(),
                    2 => CommentLog::where('id', $dtoWordBody->contentLogId)->first(),
                };

                $tableName = match ($dtoWordBody->type) {
                    1 => 'post_logs',
                    2 => 'comment_logs',
                };

                $usageType = match ($dtoWordBody->type) {
                    1 => OperationUsage::TYPE_POST_LOG,
                    2 => OperationUsage::TYPE_COMMENT_LOG,
                };
            break;
        }

        $fileIds = FileUsage::where('table_name', $tableName)->where('table_column', 'id')->where('table_id', $model->id)->pluck('file_id')->toArray();
        FileUsage::where('table_name', $tableName)->where('table_column', 'id')->where('table_id', $model->id)->forceDelete();

        OperationUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->forceDelete();
        ArchiveUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->forceDelete();
        ExtendUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->forceDelete();

        HashtagUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->forceDelete();
        DomainLinkUsage::where('usage_type', $usageType)->where('usage_id', $model->id)->forceDelete();
        Mention::where('user_id', $model->user_id)->where('mention_type', $usageType)->where('mention_id', $model->id)->forceDelete();

        $modelAppend->forceDelete();
        $model->forceDelete();

        $fileList = File::doesntHave('fileUsages')->whereIn('id', $fileIds)->get()->groupBy('type');

        $files[File::TYPE_IMAGE] = $fileList->get(File::TYPE_IMAGE)?->pluck('id')?->all() ?? null;
        $files[File::TYPE_IMAGE] = $fileList->get(File::TYPE_VIDEO)?->pluck('id')?->all() ?? null;
        $files[File::TYPE_IMAGE] = $fileList->get(File::TYPE_AUDIO)?->pluck('id')?->all() ?? null;
        $files[File::TYPE_IMAGE] = $fileList->get(File::TYPE_DOCUMENT)?->pluck('id')?->all() ?? null;

        foreach ($files as $type => $ids) {
            if (empty($ids)) {
                continue;
            }

            \FresnsCmdWord::plugin('Fresns')->physicalDeletionFiles([
                "type" => $type,
                "fileIdsOrFids" => $ids,
            ]);
        }

        return $this->success();
    }
}
