<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Exceptions\ApiException;
use App\Fresns\Api\Http\DTO\CommentDetailDTO;
use App\Fresns\Api\Http\DTO\InteractiveDTO;
use App\Fresns\Api\Http\DTO\PaginationDTO;
use App\Fresns\Api\Services\CommentService;
use App\Fresns\Api\Services\InteractiveService;
use App\Fresns\Api\Services\UserService;
use App\Helpers\ConfigHelper;
use App\Models\Comment;
use App\Models\CommentLog;
use App\Models\Seo;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // list
    public function list(string $cid, Request $request)
    {
    }

    // detail
    public function detail(string $cid, Request $request)
    {
        $dtoRequest = new CommentDetailDTO($request->all());

        $comment = Comment::with(['creator', 'hashtags'])->where('cid', $cid)->isEnable()->first();

        if (empty($comment)) {
            throw new ApiException(37400);
        }

        UserService::checkUserContentViewPerm($comment->created_at);

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        $seoData = Seo::where('linked_type', Seo::TYPE_COMMENT)->where('linked_id', $comment->id)->where('lang_tag', $langTag)->first();

        $item['title'] = $seoData->title ?? null;
        $item['keywords'] = $seoData->keywords ?? null;
        $item['description'] = $seoData->description ?? null;
        $data['items'] = $item;

        $service = new CommentService();
        $data['detail'] = $service->commentDetail($comment, 'detail', $langTag, $timezone, $authUser->id, $dtoRequest->mapId, $dtoRequest->mapLng, $dtoRequest->mapLat);

        return $this->success($data);
    }

    // interactive
    public function interactive(string $cid, string $type, Request $request)
    {
        $comment = Comment::with(['creator', 'hashtags'])->where('cid', $cid)->isEnable()->first();

        if (empty($comment)) {
            throw new ApiException(37400);
        }

        UserService::checkUserContentViewPerm($comment->created_at);

        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new InteractiveDTO($requestData);

        $markSet = ConfigHelper::fresnsConfigByItemKey("it_{$dtoRequest->type}_groups");
        if (! $markSet) {
            throw new ApiException(36201);
        }

        $timeOrder = $dtoRequest->timeOrder ?: 'desc';

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $service = new InteractiveService();
        $data = $service->getUsersWhoMarkIt($dtoRequest->type, InteractiveService::TYPE_COMMENT, $comment->id, $timeOrder, $langTag, $timezone, $authUserId);

        return $this->fresnsPaginate($data['paginateData'], $data['interactiveData']->total(), $data['interactiveData']->perPage());
    }

    // commentLogs
    public function commentLogs(string $cid, Request $request)
    {
        $comment = Comment::where('cid', $cid)->isEnable()->first();

        if (empty($comment)) {
            throw new ApiException(37400);
        }

        UserService::checkUserContentViewPerm($comment->created_at);

        $dtoRequest = new PaginationDTO($request->all());
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $commentLogs = CommentLog::with('user')->where('comment_id', $comment->id)->where('state', 3)->latest()->paginate($request->get('pageSize', 15));

        $commentLogList = [];
        $service = new CommentService();
        foreach ($commentLogs as $log) {
            $commentLogList[] = $service->commentLogList($log, $langTag, $timezone, $authUserId);
        }

        return $this->fresnsPaginate($commentLogList, $commentLogs->total(), $commentLogs->perPage());
    }

    // logDetail
    public function logDetail(string $cid, int $logId, Request $request)
    {
        $comment = Comment::where('cid', $cid)->isEnable()->first();

        if (empty($comment)) {
            throw new ApiException(37400);
        }

        UserService::checkUserContentViewPerm($comment->created_at);

        $log = CommentLog::where('comment_id', $comment->id)->where('id', $logId)->where('state', 3)->first();

        if (empty($log)) {
            throw new ApiException(37402);
        }

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $service = new CommentService();
        $data['detail'] = $service->commentLogDetail($log, $langTag, $timezone, $authUserId);

        return $this->success($data);
    }

    // delete
    public function delete(string $cid)
    {
        $comment = Comment::where('cid', $cid)->isEnable()->first();

        if (empty($comment)) {
            throw new ApiException(37400);
        }

        $authUser = $this->user();

        if ($comment->user_id != $authUser->id) {
            throw new ApiException(36403);
        }

        if (! $comment->commentAppend->can_delete) {
            throw new ApiException(36401);
        }

        $comment->delete();

        return $this->success();
    }
}
