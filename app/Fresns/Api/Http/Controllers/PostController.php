<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Exceptions\ApiException;
use App\Fresns\Api\Http\DTO\InteractiveDTO;
use App\Fresns\Api\Http\DTO\PaginationDTO;
use App\Fresns\Api\Http\DTO\PostDetailDTO;
use App\Fresns\Api\Http\DTO\PostFollowDTO;
use App\Fresns\Api\Http\DTO\PostListDTO;
use App\Fresns\Api\Http\DTO\PostNearbyDTO;
use App\Fresns\Api\Services\InteractiveService;
use App\Fresns\Api\Services\PostFollowService;
use App\Fresns\Api\Services\PostService;
use App\Fresns\Api\Services\UserService;
use App\Helpers\ConfigHelper;
use App\Helpers\PrimaryHelper;
use App\Models\Plugin;
use App\Models\Post;
use App\Models\PostLog;
use App\Models\PostUser;
use App\Models\Seo;
use App\Models\UserBlock;
use App\Utilities\ExtendUtility;
use App\Utilities\LbsUtility;
use App\Utilities\PermissionUtility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    // list
    public function list(Request $request)
    {
        $dtoRequest = new PostListDTO($request->all());

        // Plugin provides data
        if ($dtoRequest->contentType) {
            $dataPluginUnikey = ExtendUtility::getDataExtend($dtoRequest->contentType, 'postByAll');

            if ($dataPluginUnikey) {
                $wordBody = [
                    "header" => \request()->header(),
                    "body" => $dtoRequest->toArray(),
                ];

                $fresnsResp = \FresnsCmdWord::plugin($dataPluginUnikey)->getPostByAll($wordBody);

                return $fresnsResp->getOrigin();
            }
        }

        // Fresns provides data
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $filterGroupIdsArr = PermissionUtility::getPostFilterByGroupIds($authUserId);

        if (empty($authUserId)) {
            $postQuery = Post::with(['creator', 'group', 'hashtags'])->whereNotIn('group_id', $filterGroupIdsArr)->isEnable();
        } else {
            $blockPostIds = UserBlock::type(UserBlock::TYPE_POST)->where('user_id', $authUserId)->pluck('block_id')->toArray();
            $blockUserIds = UserBlock::type(UserBlock::TYPE_USER)->where('user_id', $authUserId)->pluck('block_id')->toArray();
            $blockHashtagIds = UserBlock::type(UserBlock::TYPE_HASHTAG)->where('user_id', $authUserId)->pluck('block_id')->toArray();

            $postQuery = Post::with(['creator', 'group', 'hashtags'])
                ->where(function ($query) use ($blockPostIds, $blockUserIds, $filterGroupIdsArr) {
                    $query
                        ->whereNotIn('id', $blockPostIds)
                        ->orWhereNotIn('user_id', $blockUserIds)
                        ->orWhereNotIn('group_id', $filterGroupIdsArr);
                });

            $postQuery->whereHas('hashtags', function ($query) use ($blockHashtagIds) {
                $query->whereNotIn('id', $blockHashtagIds);
            });
        }

        if ($dtoRequest->uidOrUsername) {
            $postConfig = ConfigHelper::fresnsConfigByItemKey('it_posts');
            if (! $postConfig) {
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

            $postQuery->where('user_id', $viewUser->id)->where('is_anonymous', 0);
        }

        if ($dtoRequest->gid) {
            $viewGroup = PrimaryHelper::fresnsModelByFsid('group', $dtoRequest->gid);

            if (empty($viewGroup) || $viewGroup->trashed()) {
                throw new ApiException(37100);
            }

            if ($viewGroup->isEnable(false)) {
                throw new ApiException(37101);
            }

            $postQuery->where('group_id', $viewGroup->id);
        }

        if ($dtoRequest->hid) {
            $viewHashtag = PrimaryHelper::fresnsModelByFsid('hashtag', $dtoRequest->gid);

            if (empty($viewHashtag)) {
                throw new ApiException(37200);
            }

            if ($viewHashtag->isEnable(false)) {
                throw new ApiException(37201);
            }

            $postQuery->when($viewHashtag->id, function ($query, $value) {
                $query->whereRelation('hashtags', 'id', $value);
            });
        }

        $postQuery->when($dtoRequest->digestState, function ($query, $value) {
            $query->where('digest_state', $value);
        });

        $postQuery->when($dtoRequest->stickyState, function ($query, $value) {
            $query->where('sticky_state', $value);
        });

        if ($dtoRequest->contentType && $dtoRequest->contentType != 'all') {
            if ($dtoRequest->contentType == 'text') {
                $postQuery->whereNull('types');
            } else {
                $postQuery->where('types', 'like', "%{$dtoRequest->contentType}%");
            }
        }

        $postQuery->when($dtoRequest->createDateGt, function ($query, $value) {
            $query->whereDate('created_at', '>=', $value);
        });

        $postQuery->when($dtoRequest->createDateLt, function ($query, $value) {
            $query->whereDate('created_at', '<=', $value);
        });

        $postQuery->when($dtoRequest->likeCountGt, function ($query, $value) {
            $query->where('like_count', '>=', $value);
        });

        $postQuery->when($dtoRequest->likeCountLt, function ($query, $value) {
            $query->where('like_count', '<=', $value);
        });

        $postQuery->when($dtoRequest->dislikeCountGt, function ($query, $value) {
            $query->where('dislike_count', '>=', $value);
        });

        $postQuery->when($dtoRequest->dislikeCountLt, function ($query, $value) {
            $query->where('dislike_count', '<=', $value);
        });

        $postQuery->when($dtoRequest->followCountGt, function ($query, $value) {
            $query->where('follow_count', '>=', $value);
        });

        $postQuery->when($dtoRequest->followCountLt, function ($query, $value) {
            $query->where('follow_count', '<=', $value);
        });

        $postQuery->when($dtoRequest->blockCountGt, function ($query, $value) {
            $query->where('block_count', '>=', $value);
        });

        $postQuery->when($dtoRequest->blockCountLt, function ($query, $value) {
            $query->where('block_count', '<=', $value);
        });

        $postQuery->when($dtoRequest->commentCountGt, function ($query, $value) {
            $query->where('comment_count', '>=', $value);
        });

        $postQuery->when($dtoRequest->commentCountGt, function ($query, $value) {
            $query->where('comment_count', '<=', $value);
        });

        $dateLimit = $this->userContentViewPerm()['dateLimit'];
        $postQuery->when($dateLimit, function ($query, $value) {
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

        $postQuery->orderBy($orderType, $orderDirection);

        $posts = $postQuery->paginate($request->get('pageSize', 15));

        $postList = [];
        $service = new PostService();
        foreach ($posts as $post) {
            $postList[] = $service->postDetail($post, 'list', $langTag, $timezone, $authUserId, $dtoRequest->mapId, $dtoRequest->mapLng, $dtoRequest->mapLat);
        }

        return $this->fresnsPaginate($postList, $posts->total(), $posts->perPage());
    }

    // detail
    public function detail(string $pid, Request $request)
    {
        $dtoRequest = new PostDetailDTO($request->all());

        $post = Post::with(['creator', 'group', 'hashtags'])->where('pid', $pid)->first();

        if (empty($post)) {
            throw new ApiException(37300);
        }

        if ($post->isEnable(false)) {
            throw new ApiException(37301);
        }

        UserService::checkUserContentViewPerm($post->created_at);

        // Plugin provides data
        $dataPluginUnikey = ConfigHelper::fresnsConfigByItemKey('post_detail_service');
        $dataPlugin = Plugin::where('unikey', $dataPluginUnikey)->isEnable()->first();

        if ($dataPlugin) {
            $wordBody = [
                "header" => \request()->header(),
                "body" => $dtoRequest->toArray(),
            ];

            $fresnsResp = \FresnsCmdWord::plugin($dataPlugin->unikey)->getPostDetail($wordBody);

            return $fresnsResp->getOrigin();
        }

        // Fresns provides data
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();

        $seoData = Seo::where('usage_type', Seo::TYPE_POST)->where('usage_id', $post->id)->where('lang_tag', $langTag)->first();

        $item['title'] = $seoData->title ?? null;
        $item['keywords'] = $seoData->keywords ?? null;
        $item['description'] = $seoData->description ?? null;
        $data['items'] = $item;

        $service = new PostService();
        $data['detail'] = $service->postDetail($post, 'detail', $langTag, $timezone, $authUser->id, $dtoRequest->mapId, $dtoRequest->mapLng, $dtoRequest->mapLat);

        return $this->success($data);
    }

    // interactive
    public function interactive(string $pid, string $type, Request $request)
    {
        $post = Post::where('pid', $pid)->isEnable()->first();

        if (empty($post)) {
            throw new ApiException(37300);
        }

        UserService::checkUserContentViewPerm($post->created_at);

        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new InteractiveDTO($requestData);

        InteractiveService::checkInteractiveSetting($dtoRequest->type, 'post');

        $orderDirection = $dtoRequest->orderDirection ?: 'desc';

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $service = new InteractiveService();
        $data = $service->getUsersWhoMarkIt($dtoRequest->type, InteractiveService::TYPE_POST, $post->id, $orderDirection, $langTag, $timezone, $authUserId);

        return $this->fresnsPaginate($data['paginateData'], $data['interactiveData']->total(), $data['interactiveData']->perPage());
    }

    // userList
    public function userList(string $pid, Request $request)
    {
        $post = Post::where('pid', $pid)->isEnable()->first();

        if (empty($post)) {
            throw new ApiException(37300);
        }

        UserService::checkUserContentViewPerm($post->created_at);

        $dtoRequest = new PaginationDTO($request->all());

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $userListData = PostUser::with('user')->where('post_id', $post->id)->latest()->paginate($request->get('pageSize', 15));

        $userList = [];
        $service = new UserService();
        foreach ($userListData as $user) {
            $userList[] = $service->userList($user, $langTag, $timezone, $authUserId);
        }

        return $this->fresnsPaginate($userList, $userListData->total(), $userListData->perPage());
    }

    // postLogs
    public function postLogs(string $pid, Request $request)
    {
        $post = Post::where('pid', $pid)->isEnable()->first();

        if (empty($post)) {
            throw new ApiException(37300);
        }

        UserService::checkUserContentViewPerm($post->created_at);

        $dtoRequest = new PaginationDTO($request->all());
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $postLogs = PostLog::with('user')->where('post_id', $post->id)->where('state', 3)->latest()->paginate($request->get('pageSize', 15));

        $postLogList = [];
        $service = new PostService();
        foreach ($postLogs as $log) {
            $postLogList[] = $service->postLogList($log, $langTag, $timezone, $authUserId);
        }

        return $this->fresnsPaginate($postLogList, $postLogs->total(), $postLogs->perPage());
    }

    // logDetail
    public function logDetail(string $pid, int $logId, Request $request)
    {
        $post = Post::where('pid', $pid)->isEnable()->first();

        if (empty($post)) {
            throw new ApiException(37300);
        }

        UserService::checkUserContentViewPerm($post->created_at);

        $log = PostLog::where('post_id', $post->id)->where('id', $logId)->where('state', 3)->first();

        if (empty($log)) {
            throw new ApiException(37302);
        }

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $service = new PostService();
        $data['detail'] = $service->postLogDetail($log, $langTag, $timezone, $authUserId);

        return $this->success($data);
    }

    // delete
    public function delete(string $pid)
    {
        $post = Post::where('pid', $pid)->first();

        if (empty($post)) {
            throw new ApiException(36400);
        }

        $authUser = $this->user();

        if ($post->user_id != $authUser->id) {
            throw new ApiException(36403);
        }

        if (! $post->postAppend->can_delete) {
            throw new ApiException(36401);
        }

        $post->delete();

        return $this->success();
    }

    // follow
    public function follow(string $type, Request $request)
    {
        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new PostFollowDTO($requestData);

        // Plugin provides data
        if ($dtoRequest->contentType) {
            $dataPluginUnikey = ExtendUtility::getDataExtend($dtoRequest->contentType, 'postByFollow');

            if ($dataPluginUnikey) {
                $wordBody = [
                    "header" => \request()->header(),
                    "body" => $dtoRequest->toArray(),
                ];

                $fresnsResp = \FresnsCmdWord::plugin($dataPluginUnikey)->getPostByFollow($wordBody);

                return $fresnsResp->getOrigin();
            }
        }

        // Fresns provides data
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();
        $userContentViewPerm = $this->userContentViewPerm();

        $postFollowService = new PostFollowService();

        switch ($dtoRequest->type) {
            // all
            case 'all':
                $posts = $postFollowService->getPostListByFollowAll($authUser->id, $dtoRequest->contentType, $userContentViewPerm['dateLimit']);
            break;

            // user
            case 'user':
                $posts = $postFollowService->getPostListByFollowUsers($authUser->id, $dtoRequest->contentType, $userContentViewPerm['dateLimit']);
            break;

            // group
            case 'group':
                $posts = $postFollowService->getPostListByFollowGroups($authUser->id, $dtoRequest->contentType, $userContentViewPerm['dateLimit']);
            break;

            // hashtag
            case 'hashtag':
                $posts = $postFollowService->getPostListByFollowHashtags($authUser->id, $dtoRequest->contentType, $userContentViewPerm['dateLimit']);
            break;
        }

        $postList = [];
        $service = new PostService();
        foreach ($posts as $post) {
            $postList[] = $service->postDetail($post, 'list', $langTag, $timezone, $authUser->id, $dtoRequest->mapId, $dtoRequest->mapLng, $dtoRequest->mapLat);
            $postList['followType'] = $postFollowService->getFollowType($post->user_id, $post->group_id, $post->hashtags, $authUser->id);
        }

        return $this->fresnsPaginate($postList, $posts->total(), $posts->perPage());
    }

    // nearby
    public function nearby(Request $request)
    {
        $dtoRequest = new PostNearbyDTO($request->all());

        // Plugin provides data
        if ($dtoRequest->contentType) {
            $dataPluginUnikey = ExtendUtility::getDataExtend($dtoRequest->contentType, 'postByNearby');

            if ($dataPluginUnikey) {
                $wordBody = [
                    "header" => \request()->header(),
                    "body" => $dtoRequest->toArray(),
                ];

                $fresnsResp = \FresnsCmdWord::plugin($dataPluginUnikey)->getPostByNearby($wordBody);

                return $fresnsResp->getOrigin();
            }
        }

        // Fresns provides data
        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUser = $this->user();
        $userContentViewPerm = $this->userContentViewPerm();

        if ($userContentViewPerm['type'] == 2) {
            throw new ApiException(35303);
        }

        $nearbyConfig = ConfigHelper::fresnsConfigByItemKeys([
            'nearby_length_km',
            'nearby_length_mi',
        ]);

        $unit = $dtoRequest->unit ?? ConfigHelper::fresnsConfigLengthUnit($langTag);
        $length = $dtoRequest->length ?? $nearbyConfig["nearby_length_{$unit}"];

        $nearbyLength = match ($unit) {
            'km' => $length,
            'mi' => $length * 0.6214,
            default => $length,
        };

        $posts = Post::query()
            ->select([
                DB::raw('*'),
                DB::raw(LbsUtility::getDistanceSql('map_longitude', 'map_latitude', $dtoRequest->mapLng, $dtoRequest->mapLat)),
            ])
            ->having('distance', '<=', $nearbyLength)
            ->orderBy('distance')
            ->paginate();

        $postList = [];
        $service = new PostService();
        foreach ($posts as $post) {
            $postList[] = $service->postDetail($post, 'list', $langTag, $timezone, $authUser->id, $dtoRequest->mapId, $dtoRequest->mapLng, $dtoRequest->mapLat);
        }

        return $this->fresnsPaginate($postList, $posts->total(), $posts->perPage());
    }
}
