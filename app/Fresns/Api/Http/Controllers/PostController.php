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
use App\Fresns\Api\Http\DTO\PostListDTO;
use App\Fresns\Api\Services\PostService;
use App\Fresns\Api\Http\DTO\PostDetailDTO;
use App\Fresns\Api\Http\DTO\PostFollowDTO;
use App\Fresns\Api\Services\PostFollowService;
use App\Helpers\ConfigHelper;
use App\Utilities\AppUtility;
use Illuminate\Support\Facades\DB;
use stdClass;

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
        // - 指定帖子类型为全部时，帖子可能在用户、小组、话题、全站二级精华帖重复出现。按下方重复帖子数据处理说明进行处理。场景举例：
        //      A用户关注了 B用户，A用户同时也关注了 a小组。B用户在 a小组发了 1篇帖子，帖子被设置为 a小组的精华帖。
        //      查询全部帖子时，关注的用户里会出现这篇帖子，关注的小组也会出现这篇帖子。
        // 
        // 重复帖子数据处理说明：
        //      - 关注的用户中展示此帖，小组中与其他类型不展示。优先级为：用户 > 小组 > 话题 > 全站二级精华帖
        // 
        // 数据来源：
        // - 根据下方要求，获取插件关联使用表 plugin_usages 类型为内容类型扩展的第一条已启用信息。
        //      @see https://fresns.cn/database/plugins/plugin-usages.html 插件关联表数据库参考
        //      - 未查到信息时，由主程序提供数据。
        //      - 关联信息为禁用状态时，由主程序提供数据。
        //      - 关联信息为启用状态时，由插件提供数据源。




        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new PostFollowDTO($requestData);

        $headers = AppHelper::getApiHeaders();

        $postFollowService = new PostFollowService(auth()->user(), $dtoRequest);

        // 插件转发
        if ($postFollowService->isPluginProvideDataSource(PostFollowService::POST_BY_FOLLOW)) {
            // todo: 调用命令字, 需要确定调用的命令字是哪个，临时写的 postByFollow
            $response = \FresnsCmdWord::plugin($postFollowService->getPluginUnikey(PostFollowService::POST_BY_FOLLOW))->postByFollow($dtoRequest->toArray());

            return $response->getOrigin();
        }

        // 主程序处理
        ['data' => $data, 'posts' => $posts] = $postFollowService->handle();

        if (!$posts) {
            return $this->success();
        }

        return $this->fresnsPaginate(
            $data,
            $posts->total(),
            $posts->perPage(),
        );
    }

    public function nearby()
    {
        $requestData = \request()->all();
        $requestData['type'] = 'all';
        // $dtoRequest = new PostFollowDTO($requestData);
        $dtoRequest = new stdClass();
        $dtoRequest->mapLng = '121.137543';
        $dtoRequest->mapLat = '31.4171';

        $headers = AppHelper::getApiHeaders();

        $postFollowService = new PostFollowService(auth()->user(), $dtoRequest);

        // AppUtility::ensureDistanceFunctionExists();

        $postFollowService = new PostFollowService(auth()->id(), $dtoRequest);

        // 插件转发
        if ($postFollowService->isPluginProvideDataSource(PostFollowService::POST_BY_NEAR_BY)) {
            // todo: 调用命令字, 需要确定调用的命令字是哪个，临时写的 postByFollow
            $response = \FresnsCmdWord::plugin($postFollowService->getPluginUnikey(PostFollowService::POST_BY_NEAR_BY))->postByFollow($dtoRequest->toArray());

            return $response->getOrigin();
        }

        // @see https://fresns.cn/database/keyname/interactive.html 单位 km
        $nearbyLength = ConfigHelper::fresnsConfigByItemKey('nearby_length');

        $posts = Post::query()
            ->select([
                DB::raw("*"),
                DB::raw(AppUtility::getDistanceSql('map_longitude', 'map_latitude', $dtoRequest->mapLng, $dtoRequest->mapLat))
            ])
            ->having('distance', '<=', $nearbyLength)
            ->orderBy('distance')
            ->paginate();

        // 主程序处理
        ['data' => $data, 'posts' => $posts] = $postFollowService->getPostList($posts, null, function ($post, $postItem) {
            unset($postItem['followType']);

            $postItem['distance'] = $post->distance;

            return $postItem;
        });

        if (!$posts) {
            return $this->success();
        }

        return $this->fresnsPaginate(
            $data,
            $posts->total(),
            $posts->perPage(),
        );
    }
}
