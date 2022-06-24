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
use App\Models\CommentLog;
use App\Models\PostLog;
use App\Models\SessionLog;
use App\Utilities\ConfigUtility;
use App\Utilities\PermissionUtility;
use Illuminate\Http\Request;

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
            'anonymous' => $dtoRequest->anonymous,
            'hname' => $dtoRequest->hname,
            'gid' => $dtoRequest->gid,
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
}
