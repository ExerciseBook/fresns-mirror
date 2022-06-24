<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Exceptions\ApiException;
use App\Fresns\Api\Http\DTO\EditorCreateDTO;
use App\Fresns\Api\Http\DTO\EditorDraftsDTO;
use App\Fresns\Api\Services\CommentService;
use App\Fresns\Api\Services\PostService;
use App\Helpers\ConfigHelper;
use App\Helpers\DateHelper;
use App\Helpers\PrimaryHelper;
use App\Models\CommentLog;
use App\Models\PostLog;
use App\Models\SessionLog;
use App\Utilities\ConfigUtility;
use App\Utilities\PermissionUtility;
use App\Utilities\ValidationUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EditorController extends Controller
{
    // config
    public function config($type)
    {
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        switch ($type) {
            // post
            case 'post':
                $config['editor'] = ConfigUtility::getEditorConfigByType($authUser->id, 'post', $langTag);
                $config['publish'] = ConfigUtility::getPublishConfigByType($authUser->id, 'post', $langTag, $timezone);
                $config['edit'] = ConfigUtility::getEditConfigByType('post');
            break;

            // comment
            case 'comment':
                $config['editor'] = ConfigUtility::getEditorConfigByType($authUser->id, 'comment', $langTag);
                $config['publish'] = ConfigUtility::getPublishConfigByType($authUser->id, 'comment', $langTag, $timezone);
                $config['edit'] = ConfigUtility::getEditConfigByType('comment');
            break;

            // default
            default:
                throw new ApiException(30002);
            break;
        }

        return $this->success($config);
    }

    // drafts
    public function drafts($type, Request $request)
    {
        $dtoRequest = new EditorDraftsDTO($request->all());

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        $status = [1, 2, 4];
        if ($dtoRequest->status == 1) {
            $status = [1, 4];
        }
        if ($dtoRequest->status == 2) {
            $status = [2];
        }

        $draftList = [];
        switch ($type) {
            // post
            case 'post':
                $drafts = PostLog::with('creator')
                    ->where('user_id', $authUser->id)
                    ->whereIn('state', $status)
                    ->latest()
                    ->paginate($request->get('pageSize', 15));

                $service = new PostService();
                foreach ($drafts as $draft) {
                    $draftList[] = $service->postLogList($draft, $langTag, $timezone, $authUser->id);
                }
            break;

            // comment
            case 'comment':
                $drafts = CommentLog::with('user')
                    ->where('user_id', $authUser->id)
                    ->whereIn('state', $status)
                    ->latest()
                    ->paginate($request->get('pageSize', 15));

                $service = new CommentService();
                foreach ($drafts as $draft) {
                    $draftList[] = $service->commentLogList($draft, $langTag, $timezone, $authUser->id);
                }
            break;

            // default
            default:
                throw new ApiException(30002);
            break;
        }

        return $this->fresnsPaginate($draftList, $drafts->total(), $drafts->perPage());
    }

    // create
    public function create($type, Request $request)
    {
        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new EditorCreateDTO($requestData);

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        $userRolePerm = PermissionUtility::getUserMainRolePerm($authUser->id);

        switch ($dtoRequest->type) {
            // post
            case 'post':
                if (! $userRolePerm['post_publish']) {
                    throw new ApiException(36104);
                }

                $checkLogCount = PostLog::where('user_id', $authUser->id)->whereIn('state', [1, 2, 4])->count();

                if ($checkLogCount >= $userRolePerm['post_draft_count']) {
                    throw new ApiException(38106);
                }
            break;

            // comment
            case 'comment':
                if (! $userRolePerm['comment_publish']) {
                    throw new ApiException(36104);
                }

                $checkLogCount = CommentLog::where('user_id', $authUser->id)->whereIn('state', [1, 2, 4])->count();

                if ($checkLogCount >= $userRolePerm['comment_draft_count']) {
                    throw new ApiException(38106);
                }
            break;
        }

        $wordType = match ($dtoRequest->type) {
            'post' => 1,
            'comment' => 2,
        };

        $wordBody = [
            'uid' => $authUser->uid,
            'type' => $wordType,
            'source' => $dtoRequest->source,
            'editorUnikey' => $dtoRequest->editorUnikey,
            'fsid' => $dtoRequest->fsid,
            'pid' => $dtoRequest->pid,
            'gid' => $dtoRequest->gid,
            'hname' => $dtoRequest->hname,
            'isAnonymous' => $dtoRequest->isAnonymous,
        ];
        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->generateDraft($wordBody);

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->errorResponse();
        }

        // session log
        $logType = match ($type) {
            'post' => SessionLog::TYPE_CREATE_POST_DRAFT,
            'comment' => SessionLog::TYPE_CREATE_COMMENT_DRAFT,
        };
        $sessionLog = [
            'type' => $logType,
            'pluginUnikey' => 'Fresns',
            'platformId' => $this->platformId(),
            'version' => $this->version(),
            'langTag' => $langTag,
            'aid' => $this->account()->aid,
            'uid' => $authUser->uid,
            'objectName' => route('api.editor.post.create'),
            'objectAction' => 'Editor Create Post Log',
            'objectResult' => SessionLog::STATE_SUCCESS,
            'objectOrderId' => null,
            'deviceInfo' => $this->deviceInfo(),
            'deviceToken' => null,
            'moreJson' => null,
        ];

        // upload session log
        \FresnsCmdWord::plugin('Fresns')->uploadSessionLog($sessionLog);

        switch ($dtoRequest->type) {
            // post
            case 'post':
                $service = new PostService();

                $postLog = PostLog::where('id', $fresnsResp->getData('logId'))->first();
                $data['detail'] = $service->postLogDetail($postLog, $langTag, $timezone);
            break;

            // comment
            case 'comment':
                $service = new CommentService();

                $commentLog = CommentLog::where('id', $fresnsResp->getData('logId'))->first();
                $data['detail'] = $service->commentLogDetail($commentLog, $langTag, $timezone);
            break;
        }

        $edit['editableTime'] = $fresnsResp->getData('editableTime');
        $edit['deadlineTime'] = $fresnsResp->getData('deadlineTime');
        $data['edit'] = $edit;

        return $this->success($data);
    }

    // detail
    public function detail($type, $draftId)
    {
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        $draft = match ($type) {
            'post' => PostLog::with('creator')->where('id', $draftId)->where('user_id', $authUser->id)->first(),
            'comment' => CommentLog::with('creator')->where('id', $draftId)->where('user_id', $authUser->id)->first(),
            default => null,
        };

        if (empty($draft)) {
            throw new ApiException(38100);
        }

        if ($draft->state == 2) {
            throw new ApiException(38101);
        }

        if ($draft->state == 3) {
            throw new ApiException(38102);
        }

        $editableStatus = true;
        $editableTime = null;
        $deadlineTime = null;

        $editTimeConfig = ConfigHelper::fresnsConfigByItemKey("{$type}_edit_time_limit");

        switch ($type) {
            // post
            case 'post':
                $service = new PostService();
                $data['detail'] = $service->postLogList($draft, $langTag, $timezone, $authUser->id);

                if (! $draft->post_id) {
                    $post = PrimaryHelper::fresnsModelById('post', $draft->post_id);

                    $checkContentEditPerm = PermissionUtility::checkContentEditPerm($post->created_at, $editTimeConfig, $timezone, $langTag);
                    $editableStatus = $checkContentEditPerm['editableStatus'];
                    $editableTime = $checkContentEditPerm['editableTime'];
                    $deadlineTime = $checkContentEditPerm['deadlineTime'];
                }
            break;

            // comment
            case 'comment':
                $service = new CommentService();
                $data['detail'] = $service->commentLogDetail($draft, $langTag, $timezone, $authUser->id);

                if (! $draft->comment_id) {
                    $comment = PrimaryHelper::fresnsModelById('comment', $draft->comment_id);

                    $checkContentEditPerm = PermissionUtility::checkContentEditPerm($comment->created_at, $editTimeConfig, $timezone, $langTag);
                    $editableStatus = $checkContentEditPerm['editableStatus'];
                    $editableTime = $checkContentEditPerm['editableTime'];
                    $deadlineTime = $checkContentEditPerm['deadlineTime'];
                }
            break;
        }

        $edit['editableStatus'] = $editableStatus;
        $edit['editableTime'] = $editableTime;
        $edit['deadlineTime'] = $deadlineTime;
        $data['edit'] = $edit;

        return $this->success($data);
    }

    // publish
    public function publish($type, $draftId)
    {
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        $draft = match ($type) {
            'post' => PostLog::with('creator')->where('id', $draftId)->where('user_id', $authUser->id)->first(),
            'comment' => CommentLog::with('creator')->where('id', $draftId)->where('user_id', $authUser->id)->first(),
            default => null,
        };

        if (empty($draft)) {
            throw new ApiException(38100);
        }

        if ($draft->state == 2) {
            throw new ApiException(38103);
        }

        if ($draft->state == 3) {
            throw new ApiException(38104);
        }

        $editorConfig = ConfigHelper::fresnsConfigByItemKeys([
            "{$type}_editor_title_length",
            "{$type}_editor_content_length",
            "{$type}_edit_time_limit",
            'content_review_service',
        ]);

        if ($draft->title) {
            $titleLength = Str::length($draft->title);
            if ($titleLength > $editorConfig['post_editor_title_length']) {
                throw new ApiException(38202);
            }

            $checkTitleBanWords = ValidationUtility::contentBanWords($draft->title);
            if (! $checkTitleBanWords) {
                throw new ApiException(38205);
            }
        }

        if (! $draft->content) {
            throw new ApiException(38203);
        } else {
            $contentLength = Str::length($draft->content);
            if ($contentLength > $editorConfig["{$type}_editor_content_length"]) {
                throw new ApiException(38204);
            }

            $checkContentBanWords = ValidationUtility::contentBanWords($draft->content);
            if (! $checkContentBanWords) {
                throw new ApiException(38205);
            }
        }

        $publishConfig = ConfigUtility::getPublishConfigByType($authUser->id, $type, $langTag, $timezone);

        if (! $publishConfig['perm']['publish']) {
            return $this->failure([
                36104,
                ConfigUtility::getCodeMessage(36104, 'Fresns', $langTag),
                $publishConfig['perm']['tips'],
            ]);
        }

        if ($publishConfig['limit']['status']) {
            switch ($publishConfig['limit']['type']) {
                // period Y-m-d H:i:s
                case 1:
                    $dbDateTime = DateHelper::fresnsDatabaseCurrentDateTime();
                    $newDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dbDateTime);
                    $periodStart = Carbon::createFromFormat('Y-m-d H:i:s', $publishConfig['limit']['periodStart']);
                    $periodEnd = Carbon::createFromFormat('Y-m-d H:i:s', $publishConfig['limit']['periodEnd']);

                    $isInTime = $newDateTime->between($periodStart, $periodEnd);
                    if ($isInTime) {
                        throw new ApiException(36304);
                    }
                break;

                // cycle H:i
                case 2:
                    $dbDateTime = DateHelper::fresnsDatabaseCurrentDateTime();
                    $newDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $dbDateTime);
                    $dbDate = date('Y-m-d', $dbDateTime);
                    $cycleStart = "{$dbDate} {$publishConfig['limit']['cycleStart']}:00"; // Y-m-d H:i:s
                    $cycleEnd = "{$dbDate} {$publishConfig['limit']['cycleEnd']}:00"; // Y-m-d H:i:s

                    $periodStart = Carbon::createFromFormat('Y-m-d H:i:s', $cycleStart); // 2022-07-01 22:30:00
                    $periodEnd = Carbon::createFromFormat('Y-m-d H:i:s', $cycleEnd); // 2022-07-01 08:30:00

                    if ($periodEnd->lt($periodStart)) {
                        // next day 2022-07-02 08:30:00
                        $periodEnd = $periodEnd->addDay();
                    }

                    $isInTime = $newDateTime->between($periodStart, $periodEnd);
                    if ($isInTime) {
                        throw new ApiException(36304);
                    }
                break;
            }
        }

        // session log
        $sessionLogType = match ($type) {
            'post' => SessionLog::TYPE_RELEASE_POST,
            'comment' => SessionLog::TYPE_RELEASE_COMMENT,
        };
        $sessionLog = [
            'type' => $sessionLogType,
            'pluginUnikey' => 'Fresns',
            'platformId' => $this->platformId(),
            'version' => $this->version(),
            'langTag' => $this->langTag(),
            'aid' => $this->account()->aid,
            'uid' => $authUser->uid,
            'objectName' => route('api.editor.publish'),
            'objectAction' => 'Editor Publish',
            'objectResult' => SessionLog::STATE_UNKNOWN,
            'objectOrderId' => $draft->id,
            'deviceInfo' => $this->deviceInfo(),
            'deviceToken' => null,
            'moreJson' => null,
        ];

        // cmd word
        $wordType = match ($type) {
            'post' => 1,
            'comment' => 2,
        };
        $wordBody = [
            'type' => $wordType,
            'logId' => $draft->id,
        ];

        switch ($type) {
            // post
            case 'post':
                if (! $draft->post_id) {
                    $post = PrimaryHelper::fresnsModelById('post', $draft->post_id);

                    $checkContentEditPerm = PermissionUtility::checkContentEditPerm($post->created_at, $$editorConfig['post_edit_time_limit'], $timezone, $langTag);

                    if (! $checkContentEditPerm['editableStatus']) {
                        throw new ApiException(36309);
                    }
                }

                if (! $draft->group_id) {
                    $group = PrimaryHelper::fresnsModelById('group', $draft->group_id);

                    if (! $group) {
                        throw new ApiException(37100);
                    }

                    if ($group->is_enable == 0) {
                        throw new ApiException(37101);
                    }

                    $checkGroup = PermissionUtility::checkUserGroupPublishPerm($draft->group_id, $group->permissions, $draft->user_id);

                    if (! $checkGroup['allowPost']) {
                        throw new ApiException(36311);
                    }

                    if ($checkGroup['reviewPost']) {

                        // upload session log
                        \FresnsCmdWord::plugin('Fresns')->uploadSessionLog($sessionLog);

                        // change state
                        $draft->update([
                            'state' => 2,
                        ]);

                        // review notice
                        \FresnsCmdWord::plugin($editorConfig['content_review_service'])->reviewNotice($wordBody);

                        // Review
                        throw new ApiException(38200);
                    }
                }
            break;

            // comment
            case 'comment':
                if (! $draft->comment_id) {
                    $comment = PrimaryHelper::fresnsModelById('comment', $draft->comment_id);

                    $checkContentEditPerm = PermissionUtility::checkContentEditPerm($comment->created_at, $editorConfig['comment_edit_time_limit'], $timezone, $langTag);

                    if (! $checkContentEditPerm['editableStatus']) {
                        throw new ApiException(36309);
                    }
                }

                $post = PrimaryHelper::fresnsModelById('post', $draft->post_id);
                if (! $post->group_id) {
                    $group = PrimaryHelper::fresnsModelById('group', $draft->group_id);

                    if (! $group) {
                        throw new ApiException(37100);
                    }

                    if ($group->is_enable == 0) {
                        throw new ApiException(37101);
                    }

                    $checkGroup = PermissionUtility::checkUserGroupPublishPerm($draft->group_id, $group->permissions, $draft->user_id);

                    if (! $checkGroup['allowComment']) {
                        throw new ApiException(36312);
                    }

                    if ($checkGroup['reviewComment']) {

                        // upload session log
                        \FresnsCmdWord::plugin('Fresns')->uploadSessionLog($sessionLog);

                        // change state
                        $draft->update([
                            'state' => 2,
                        ]);

                        // review notice
                        \FresnsCmdWord::plugin($editorConfig['content_review_service'])->reviewNotice($wordBody);

                        // Review
                        throw new ApiException(38200);
                    }
                }
            break;
        }

        $checkReview = ValidationUtility::contentReviewWords($draft->content);
        if ($checkReview) {
            // upload session log
            \FresnsCmdWord::plugin('Fresns')->uploadSessionLog($sessionLog);

            // change state
            $draft->update([
                'state' => 2,
            ]);

            // review notice
            \FresnsCmdWord::plugin($editorConfig['content_review_service'])->reviewNotice($wordBody);

            // Review
            throw new ApiException(38200);
        }

        $fresnsResp = \FresnsCmdWord::plugin('Fresns')->releaseContent($wordBody);

        if ($fresnsResp->isErrorResponse()) {
            return $fresnsResp->errorResponse();
        }

        // upload session log
        $sessionLog['objectResult'] = SessionLog::STATE_SUCCESS;
        $sessionLog['objectOrderId'] = $fresnsResp->getData('id');
        \FresnsCmdWord::plugin('Fresns')->uploadSessionLog($sessionLog);

        return $this->success();
    }
}
