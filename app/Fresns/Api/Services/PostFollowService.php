<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\UserFollow;
use App\Models\PluginUsage;
use Illuminate\Support\Str;
use App\Models\HashtagLinked;
use App\Fresns\Api\Services\PostService;
use App\Utilities\AppUtility;
use RuntimeException;

class PostFollowService
{
    const POST_BY_ALL = 'postByAll';
    const POST_BY_FOLLOW = 'postByFollow';
    const POST_BY_NEAR_BY = 'postByNearby';

    /**
     * @var User $user
     */
    protected $user;

    protected $userId;

    protected $postService;

    protected $dtoRequest;

    public function __construct($user, $dtoRequest = null)
    {
        $this->user = $user;

        $this->userId = $user?->id;
        $this->postService = new PostService();
        $this->dtoRequest = $dtoRequest;
    }

    public function isPluginProvideDataSource(string $type)
    {
        $this->pluginUseage = PluginUsage::type(PluginUsage::TYPE_CONTENT)->isEnable()->first();
        $pluginUnikey = $this->getPluginUnikey($type);

        return (bool) $pluginUnikey;
    }

    public function getPluginUnikey(string $type)
    {
        return $this->pluginUseage->data_sources[$type]['pluginUnikey'] ?? null;
    }

    public function handle()
    {
        if (AppUtility::isForbidden($this->user)) {
            throw new \RuntimeException('您当前不是会员, 无法访问');
        }

        $method = sprintf("get%sFollow", Str::studly($this->dtoRequest->type)); // getAllFollow、getUserFollow、getGroupFollow、getHashtagFollow

        if (!method_exists($this, $method)) {
            throw new \RuntimeException(
                sprintf('unknow method %s::%s', get_class($this), $method)
            );
        }

        return $this->$method();
    }

    public function getPostList($posts, ?string $followType = null, ?callable $callable = null)
    {
        $postList = [];
        foreach ($posts as $post) {
            // todo: 转换详情信息
            // $postItem = $this->postSservice->postDetail($post->id, 'list', $this->dtoRequest->mapId, $this->dtoRequest->mapLng, $this->dtoRequest->mapLat);

            $postItem['followType'] = $followType;
            $postItem['pid'] = $post->pid;

            if ($callable) {
                $postItem = $callable($post, $postItem);
            }

            $postList[] = $postItem;
        }

        return [
            'posts' => $posts,
            'data' => $postList,
        ];
    }

    public function getAllFollow()
    {
        // 自己的帖子
        $userIds = [$this->userId];
        // 我关注的用户的帖子
        $followerUserIds = $this->getFollowIdsByType(UserFollow::TYPE_USER);
        $followerUserIds = array_merge($userIds, $followerUserIds);

        $postQueryFollowers = Post::whereIn('user_id', $followerUserIds)->latest();

        // 我关注的小组中的二级精华帖子
        $followerGroupIds = $this->getFollowIdsByType(UserFollow::TYPE_GROUP);
        $postQueryGroups = Post::whereNotIn('user_id', $followerUserIds)->whereIn('group_id', $followerGroupIds)->whereIn('digest_state', [2, 3])->latest();

        // 我关注的话题的二级精华帖子
        $followerIds = $this->getFollowIdsByType(UserFollow::TYPE_HASHTAG);
        $hashtagPostIds = $this->getPostIdsByHashTag($followerIds);
        $postQueryHashtags = Post::whereNotIn('user_id', $followerUserIds)->whereNotIn('group_id', $followerGroupIds)->whereIn('id', $hashtagPostIds)->whereIn('digest_state', [2, 3])->latest();

        // 全站二级精华帖子
        $postQuerySites = Post::whereNotIn('user_id', $followerUserIds)->whereNotIn('group_id', $followerGroupIds)->whereNotIn('id', $hashtagPostIds)->whereIn('digest_state', [2, 3])->latest();

        // 查询数据
        $posts = $postQuerySites
            ->union($postQueryFollowers)
            ->union($postQueryGroups)
            ->union($postQueryHashtags)
            ->beforeExpiredAtOrNotLimit($this->user)
            ->latest()
            ->paginate(1000);

        return $this->getPostList($posts, null, function ($post, $postItem) use ($followerUserIds, $followerGroupIds, $hashtagPostIds) {
            $followType = match (true) {
                default => 'unknown',
                in_array($post->user_id, $followerUserIds) => 'user',
                in_array($post->group_id, $followerGroupIds) => 'group',
                in_array($post->id, $hashtagPostIds) => 'hashtag',
                in_array($post->digest_state, [2, 3]) => 'digest',
            };

            $postItem['followType'] = $followType;
            $postItem['user_id'] = $post->user_id;
            $postItem['group_id'] = $post->group_id;
            $postItem['id'] = $post->id;
            $postItem['digest_state'] = $post->digest_state;
            $postItem['created_at'] = $post->created_at->toDateTimeString();

            return $postItem;
        });
    }

    public function getUserFollow()
    {
        $followerIds = $this->getFollowIdsByType(UserFollow::TYPE_USER);

        $followerIds = array_merge($followerIds, [$this->userId]);

        $posts = Post::query()
            ->whereIn('user_id', $followerIds)
            ->beforeExpiredAtOrNotLimit($this->user)
            ->latest()
            ->paginate();

        return $this->getPostList($posts, 'user');
    }

    public function getGroupFollow()
    {
        $followerIds = $this->getFollowIdsByType(UserFollow::TYPE_GROUP);

        $posts = Post::query()
            ->where('user_id', $this->userId)
            ->whereIn('group_id', $followerIds)
            ->beforeExpiredAtOrNotLimit($this->user)
            ->latest()
            ->paginate();

        return $this->getPostList($posts, 'group');
    }

    public function getHashtagFollow()
    {
        // todo: 获取话题帖子功能需要验证。账号下没有数据，暂时不确定功能是否正常。
        // 获取用户关注的话题
        // 获取话题下的所有帖子
        $followerIds = $this->getFollowIdsByType(UserFollow::TYPE_HASHTAG);

        $postIds = $this->getPostIdsByHashTag($followerIds);

        $postQuery = Post::whereIn('id', $postIds);

        $posts = Post::when($this->userId, function ($query, $userId) {
            $query->where('user_id', $userId)->latest();
        })
            ->union($postQuery)
            ->beforeExpiredAtOrNotLimit($this->user)
            ->latest()
            ->paginate();

        return $this->getPostList($posts, 'hashtag');
    }

    protected function getFollowIdsByType(int $type)
    {
        return UserFollow::query()
            ->when($this->userId, function ($query, $userId) {
                $query->where('user_id', $userId)->latest();
            })
            ->type($type)
            ->pluck('follow_id')
            ->toArray();
    }

    protected function getPostIdsByHashTag(array $followerIds)
    {
        return HashtagLinked::whereIn('hashtag_id', $followerIds)->where('linked_type', HashtagLinked::TYPE_POST)->pluck('linked_id')->toArray();
    }
}
