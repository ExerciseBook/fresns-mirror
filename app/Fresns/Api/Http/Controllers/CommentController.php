<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Exceptions\ApiException;
use App\Fresns\Api\Http\DTO\CommentDetailDTO;
use App\Fresns\Api\Http\DTO\CommentListDTO;
use App\Fresns\Api\Http\DTO\InteractiveDTO;
use App\Fresns\Api\Http\DTO\PaginationDTO;
use App\Fresns\Api\Services\CommentService;
use App\Fresns\Api\Services\InteractiveService;
use App\Fresns\Api\Services\UserService;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\Comment;
use App\Models\CommentLog;
use App\Models\Seo;
use App\Models\UserBlock;
use App\Utilities\PermissionUtility;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // list
    public function list(Request $request)
    {
        $dtoRequest = new CommentListDTO($request->all());

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $filterGroupIdsArr = PermissionUtility::getPostFilterByGroupIds($authUserId);

        if (empty($authUserId)) {
            $commentQuery = Comment::with(['creator', 'post', 'hashtags'])->isEnable();
        } else {
            $blockCommentIds = UserBlock::type(UserBlock::TYPE_COMMENT)->where('user_id', $authUserId)->pluck('block_id')->toArray();
            $blockUserIds = UserBlock::type(UserBlock::TYPE_USER)->where('user_id', $authUserId)->pluck('block_id')->toArray();
            $blockHashtagIds = UserBlock::type(UserBlock::TYPE_HASHTAG)->where('user_id', $authUserId)->pluck('block_id')->toArray();

            $commentQuery = Comment::with(['creator', 'group', 'hashtags'])
                ->where(function ($query) use ($blockCommentIds, $blockUserIds) {
                    $query->whereNotIn('id', $blockCommentIds)->orWhereNotIn('user_id', $blockUserIds);
                });

            $commentQuery->whereHas('hashtags', function ($query) use ($blockHashtagIds) {
                $query->whereNotIn('id', $blockHashtagIds);
            });
        }

        $commentQuery->whereHas('post', function ($query) use ($filterGroupIdsArr) {
            $query->whereNotIn('group_id', $filterGroupIdsArr);
        });

        if ($dtoRequest->uidOrUsername) {
            $commentConfig = ConfigHelper::fresnsConfigByItemKey('it_comments');
            if (! $commentConfig) {
                throw new ApiException(35305);
            }

            $viewUser = PrimaryHelper::fresnsModelByFsid('user', $dtoRequest->uidOrUsername);

            if (empty($viewUser) || $viewUser->trashed()) {
                throw new ApiException(31602);
            }

            if ($viewUser->isEnable(false)) {
                throw new ApiException(35202);
            }

            if ($viewUser->wait_delete == 1) {
                throw new ApiException(35203);
            }

            $commentQuery->where('user_id', $viewUser->id)->where('is_anonymous', 0);
        }

        if ($dtoRequest->pid) {
            $viewPost = PrimaryHelper::fresnsModelByFsid('post', $dtoRequest->pid);

            if (empty($viewPost) || $viewPost->trashed()) {
                throw new ApiException(37300);
            }

            if ($viewPost->isEnable(false)) {
                throw new ApiException(37301);
            }

            $commentQuery->where('post_id', $viewPost->id);
        }

        if ($dtoRequest->cid) {
            $viewComment = PrimaryHelper::fresnsModelByFsid('comment', $dtoRequest->cid);

            if (empty($viewComment) || $viewComment->trashed()) {
                throw new ApiException(37400);
            }

            if ($viewComment->isEnable(false)) {
                throw new ApiException(37401);
            }

            $commentQuery->where('comment_id', $viewComment->id);
        }

        if ($dtoRequest->gid) {
            $viewGroup = PrimaryHelper::fresnsModelByFsid('group', $dtoRequest->gid);

            if (empty($viewGroup) || $viewGroup->trashed()) {
                throw new ApiException(37100);
            }

            if ($viewGroup->isEnable(false)) {
                throw new ApiException(37101);
            }

            $groupId = $viewGroup->id;

            $commentQuery->whereHas('post', function ($query) use ($groupId) {
                $query->where('group_id', $groupId);
            });
        }

        if ($dtoRequest->hid) {
            $viewHashtag = PrimaryHelper::fresnsModelByFsid('hashtag', $dtoRequest->hid);

            if (empty($viewHashtag)) {
                throw new ApiException(37200);
            }

            if ($viewHashtag->isEnable(false)) {
                throw new ApiException(37201);
            }

            $commentQuery->when($viewHashtag->id, function ($query, $value) {
                $query->whereRelation('hashtags', 'id', $value);
            });
        }

        $commentQuery->when($dtoRequest->sticky, function ($query, $value) {
            $query->where('is_sticky', $value);
        });

        $commentQuery->when($dtoRequest->digestState, function ($query, $value) {
            $query->where('digest_state', $value);
        });

        $commentQuery->when($dtoRequest->contentType, function ($query, $value) {
            $query->where('types', 'like', "%$value%");
        });

        $commentQuery->when($dtoRequest->createDateGt, function ($query, $value) {
            $query->whereDate('created_at', '>=', $value);
        });

        $commentQuery->when($dtoRequest->createDateLt, function ($query, $value) {
            $query->whereDate('created_at', '<=', $value);
        });

        $commentQuery->when($dtoRequest->likeCountGt, function ($query, $value) {
            $query->where('like_count', '>=', $value);
        });

        $commentQuery->when($dtoRequest->likeCountLt, function ($query, $value) {
            $query->where('like_count', '<=', $value);
        });

        $commentQuery->when($dtoRequest->dislikeCountGt, function ($query, $value) {
            $query->where('dislike_count', '>=', $value);
        });

        $commentQuery->when($dtoRequest->dislikeCountLt, function ($query, $value) {
            $query->where('dislike_count', '<=', $value);
        });

        $commentQuery->when($dtoRequest->followCountGt, function ($query, $value) {
            $query->where('follow_count', '>=', $value);
        });

        $commentQuery->when($dtoRequest->followCountLt, function ($query, $value) {
            $query->where('follow_count', '<=', $value);
        });

        $commentQuery->when($dtoRequest->blockCountGt, function ($query, $value) {
            $query->where('block_count', '>=', $value);
        });

        $commentQuery->when($dtoRequest->blockCountLt, function ($query, $value) {
            $query->where('block_count', '<=', $value);
        });

        $commentQuery->when($dtoRequest->commentCountGt, function ($query, $value) {
            $query->where('comment_count', '>=', $value);
        });

        $commentQuery->when($dtoRequest->commentCountGt, function ($query, $value) {
            $query->where('comment_count', '<=', $value);
        });

        $dateLimit = $this->userContentViewPerm()['dateLimit'];
        $commentQuery->when($dateLimit, function ($query, $value) {
            $query->where('created_at', '<=', $value);
        });

        $orderType = match ($dtoRequest->orderType) {
            default => 'created_at',
            'createDate' => 'created_at',
            'like' => 'like_count',
            'dislike' => 'dislike_count',
            'follow' => 'follow_count',
            'block' => 'block_count',
            'comment' => 'comment_count',
        };

        $orderDirection = match ($dtoRequest->orderDirection) {
            default => 'desc',
            'asc' => 'asc',
            'desc' => 'desc',
        };

        $commentQuery->orderBy($orderType, $orderDirection);

        $posts = $commentQuery->paginate($request->get('pageSize', 15));

        $postList = [];
        $service = new CommentService();
        foreach ($posts as $post) {
            $postList[] = $service->commentDetail($post, 'list', $langTag, $timezone, $authUserId, $dtoRequest->mapId, $dtoRequest->mapLng, $dtoRequest->mapLat);
        }

        return $this->fresnsPaginate($postList, $posts->total(), $posts->perPage());
    }

    // detail
    public function detail(string $cid, Request $request)
    {
        $dtoRequest = new CommentDetailDTO($request->all());

        $comment = Comment::with(['creator', 'hashtags'])->where('cid', $cid)->first();

        if (empty($comment)) {
            throw new ApiException(37400);
        }

        if ($comment->isEnable(false)) {
            throw new ApiException(37401);
        }

        UserService::checkUserContentViewPerm($comment->created_at);

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        $seoData = Seo::where('usage_type', Seo::TYPE_COMMENT)->where('usage_id', $comment->id)->where('lang_tag', $langTag)->first();

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
        $comment = Comment::where('cid', $cid)->isEnable()->first();

        if (empty($comment)) {
            throw new ApiException(37400);
        }

        UserService::checkUserContentViewPerm($comment->created_at);

        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new InteractiveDTO($requestData);

        InteractiveService::checkInteractiveSetting($dtoRequest->type, 'comment');

        $orderDirection = $dtoRequest->orderDirection ?: 'desc';

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $service = new InteractiveService();
        $data = $service->getUsersWhoMarkIt($dtoRequest->type, InteractiveService::TYPE_COMMENT, $comment->id, $orderDirection, $langTag, $timezone, $authUserId);

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
