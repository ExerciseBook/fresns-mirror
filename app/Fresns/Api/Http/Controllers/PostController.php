<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Models\Seo;
use App\Models\Post;
use App\Models\User;
use App\Helpers\AppHelper;
use Illuminate\Http\Request;
use App\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Fresns\Api\Http\DTO\PostListDTO;
use App\Fresns\Api\Services\PostService;
use App\Fresns\Api\Http\DTO\PostDetailDTO;
use App\Fresns\Api\Http\DTO\PostFollowDTO;
use App\Models\HashtagLinked;
use App\Models\UserFollow;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function list(Request $request)
    {
        $dtoRequest = new PostListDTO($request->all());

        $headers = AppHelper::getApiHeaders();
        $user = !empty($headers['uid']) ? User::whereUid($headers['uid'])->first() : null;

        $postQuery = Post::where('is_enable', 1);
        $posts = $postQuery->paginate($request->get('pageSize', 10));

        $postList = [];
        foreach ($posts as $post) {
            $service = new PostService();
            $postList[] = $service->postDetail($post->id, 'list', $dtoRequest->mapId, $dtoRequest->mapLng, $dtoRequest->mapLat);
        }

        return $this->fresnsPaginate($postList, $posts->total(), $posts->perPage());
    }

    public function detail(string $pid, Request $request)
    {
        $dtoRequest = new PostDetailDTO($request->all());

        $headers = AppHelper::getApiHeaders();

        $post = Post::with('creator')->wherePid($pid)->first();
        if (empty($post)) {
            throw new ApiException(37300);
        }

        $seoData = Seo::where('linked_type', 4)->where('linked_id', $post->id)->where('lang_tag', $headers['langTag'])->first();
        $common['title'] = $seoData->title ?? null;
        $common['keywords'] = $seoData->keywords ?? null;
        $common['description'] = $seoData->description ?? null;
        $data['commons'] = $common;

        $service = new PostService();
        $data['detail'] = $service->postDetail($post->id, 'detail', $dtoRequest->mapId, $dtoRequest->mapLng, $dtoRequest->mapLat);

        return $this->success($data);
    }

    public function ensureDistanceFunctionExists()
    {
        $getDistanceSqlFunctionExists = \Illuminate\Support\Facades\Cache::remember('get_distance_sql_exists', now()->addDays(7), function () {
            $getDistanceSqlFunctionSql = "SHOW FUNCTION STATUS where name = 'get_distance'";
            $getDistanceSqlFunction = \Illuminate\Support\Facades\DB::selectOne($getDistanceSqlFunctionSql);
            $getDistanceSqlFunctionExists = boolval($getDistanceSqlFunction);

            if ($getDistanceSqlFunctionExists) {
                return true;
            }

            $createGetDistanceFunctionSql = <<<SQL
drop function if exists get_distance;
delimiter //
create function get_distance (
  lng1 double,
  lat1 double,
  lng2 double,
  lat2 double
)
returns double
begin
    declare distance double;
    declare a double;
    declare b double;

    declare radLat1 double;
    declare radLat2 double;
    declare radLng1 double;
    declare radLng2 double;
 
    set radLat1 = lat1 * PI() / 180;
    set radLat2 = lat2 * PI() / 180;
    set radLng1 = lng1 * PI() / 180;
    set radLng2 = lng2 * PI() / 180;

    set a = radLat1 - radLat2;
    set b = radLng1 - radLng2;

    set distance = 2 * asin(
      sqrt(
        pow(sin(a / 2), 2) + cos(radLat1) * cos(radLat2) * pow(sin(b / 2), 2)
      )
    ) * 6378.137;
    return distance;
end//
delimiter ;
SQL;

            return \Illuminate\Support\Facades\DB::statement($createGetDistanceFunctionSql);
        });

        if (!$getDistanceSqlFunctionExists) {
            \Illuminate\Support\Facades\Cache::forget('get_distance_sql_exists');
        }

        return $getDistanceSqlFunctionExists;
    }

    public function follow(string $type, Request $request)
    {
        // todo: 获取 follow 业务逻辑
        // 业务需求：获取用户的全部帖子，小组与话题下的精华帖子，全站二级精华帖子
        // @see https://fresns.cn/api/content/post-follows.html#返回结果
        //
        // 注：
        //      - 帖子均以发布时间倒序排列。
        //      - 站点为私有模式时，需要验证用户到期状态 users.expired_at，确认用户是否已到期。
        //        并根据站点配置的到期后数据处理模式 configs.site_private_end 对数据进行处理。
        //          - 站点到期数据处理模式为 1 时，接口不允许请求。
        //          - 站点到期数据处理模式为 2 时，输出用户到期前的内容，到期之后的内容不进行输出展示。
        //      - 当帖子数据来源配置为插件提供时，由插件提供数据源。帖子是否由插件提供判断依据，见下方数据来源说明。
        //
        // - 当查询指定要查看的帖子类型 all 时，帖子涵括以下信息：
        //      - 我自己发布的所有帖子
        //      - 我关注的用户，发表的所有帖子（输出的数据中：followType = user）
        //      - 我关注的小组，被设置为精华的帖子（输出的数据中：followType = group）
        //      - 我关注的话题，被设置为精华的帖子（输出的数据中：followType = hashtag）
        //      - 全站二级精华帖子（输出的数据中：followType = digest）
        // - 当查看帖子类型为 user 时，获取我关注的用户的所有帖子，以及自己发表的所有帖子
        // - 当查看帖子类型为 group 时，获取我关注的小组的所有帖子，包括但不限于精华帖子与非精华帖子，以及自己发表的所有帖子
        // - 当查看帖子类型为 hashtag 时，获取我关注的话题的所有帖子，包括但不限于精华帖子与非精华帖子，以及自己发表的所有帖子
        // 
        // - 指定帖子类型为全部时，帖子可能在用户、小组、话题、全站二级精华帖重复出现。场景举例：
        //      A用户关注了 B用户，A用户同时也关注了 a小组。B用户在 a小组发了 1篇帖子，帖子被设置为 a小组的精华帖。
        //      查询全部帖子时，关注的用户里会出现这篇帖子，关注的小组也会出现这篇帖子。
        // 
        // 此时，需要按照如下逻辑处理：
        //      - 关注的用户中展示此帖，小组中与其他类型不展示。优先级为：用户 > 小组 > 话题 > 全站二级精华帖
        // 
        // 数据来源：
        // - 根据下方要求，获取插件关联使用表 plugin_usages 类型为内容类型扩展的第一条信息。
        //      @see https://fresns.cn/database/plugins/plugin-usages.html 插件关联表数据库参考
        //      - 未查到信息时，由主程序提供数据。
        //      - 关联信息为禁用状态时，由主程序提供数据。
        //      - 关联信息为启用状态时，由插件提供数据源。




        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new PostFollowDTO($requestData);

        $headers = AppHelper::getApiHeaders();

        $method = sprintf("get%sFollow", Str::studly($dtoRequest->type)); // getAllFollow、getUserFollow、getGroupFollow、getHashtagFollow
        if (!method_exists($this->postClass(), $method)) {
            throw new \RuntimeException("unknow method $method");
        }

        ['data' => $data, 'posts' => $posts] = $this->postClass()->$method();

        if (!$posts) {
            return $this->success();
        }

        return $this->fresnsPaginate(
            $data,
            $posts->total()
        );
    }

    public function postClass()
    {
        return new class
        {
            protected $userId;

            protected $postService;

            public function __construct()
            {
                $this->userId = auth()->id() ?? 24; // 24 为测试数据
                $this->postService = new PostService();
            }

            public function getPostList($posts, string $followType, ?callable $callable = null)
            {
                $postList = [];
                foreach ($posts as $post) {
                    $postItem['followType'] = $followType;
                    $postItem['pid'] = $post->pid ?? null;

                    // todo: 转换详情信息
                    // $postList[] = $postSservice->postDetail($post->id, 'list', $dtoRequest->mapId, $dtoRequest->mapLng, $dtoRequest->mapLat);

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
                return ['data' => [], 'posts' => null];
            }

            public function getUserFollow()
            {
                $followerIds = $this->getFollowIdsByType(UserFollow::FOLLOW_TYPE_USER);
                $posts = Post::whereIn('user_id', [$this->userId, $followerIds])->latest()->paginate();

                return $this->getPostList($posts, 'user');
            }

            public function getGroupFollow()
            {
                $followerIds = $this->getFollowIdsByType(UserFollow::FOLLOW_TYPE_GROUP);
                $posts = Post::whereIn('user_id', [$this->userId, $followerIds])->latest()->paginate();

                return $this->getPostList($posts, 'group');
            }

            public function getHashtagFollow()
            {
                // 获取用户关注的话题
                // 获取话题下的所有帖子
                $followerIds = $this->getFollowIdsByType(UserFollow::FOLLOW_TYPE_HASHTAG);

                $postIds = $this->getPostIdsByHashTag($followerIds);

                $postQuery = Post::whereIn('id', $postIds);

                $posts = Post::where('user_id', $this->userId)->union($postQuery)->latest()->paginate();

                return $this->getPostList($posts, 'hashtag');
            }

            protected function getFollowIdsByType(int $type)
            {
                return UserFollow::query()->where('user_id', $this->userId)->type($type)->pluck('follow_id')->toArray();
            }

            protected function getPostIdsByHashTag(array $followerIds)
            {
                return HashtagLinked::whereIn('hashtag_id', $followerIds)->where('linked_type', HashtagLinked::LINKED_TYPE_POST)->pluck('linked_id')->toArray();
            }
        };
    }
}
