<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Fresns\Api\Http\DTO\PostDetailDTO;
use App\Fresns\Api\Http\DTO\PostFollowDTO;
use App\Fresns\Api\Http\DTO\PostListDTO;
use App\Helpers\AppHelper;
use App\Models\Post;
use App\Models\User;
use App\Models\Seo;
use App\Exceptions\ApiException;
use App\Fresns\Api\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function list(Request $request)
    {
        $dtoRequest = new PostListDTO($request->all());

        $headers = AppHelper::getApiHeaders();
        $user = ! empty($headers['uid']) ? User::whereUid($headers['uid'])->first() : null;

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
        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new PostFollowDTO($requestData);

        $headers = AppHelper::getApiHeaders();

        switch ($dtoRequest->type) {
            // all
            case 'all':
                $data = null;
            break;

            // user
            case 'user':
                $data = null;
            break;

            // group
            case 'group':
                $data = null;
            break;

            // hashtag
            case 'hashtag':
                $data = null;
            break;
        }

        return $this->success($data);
    }
}
