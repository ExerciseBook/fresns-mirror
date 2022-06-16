<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\Controllers;

use App\Exceptions\ApiException;
use App\Fresns\Api\Http\DTO\HashtagListDTO;
use App\Fresns\Api\Http\DTO\InteractiveDTO;
use App\Fresns\Api\Services\HashtagService;
use App\Fresns\Api\Services\InteractiveService;
use App\Models\Hashtag;
use App\Models\Seo;
use App\Models\UserBlock;
use Illuminate\Http\Request;

class HashtagController extends Controller
{
    // list
    public function list(Request $request)
    {
        $dtoRequest = new HashtagListDTO($request->all());

        $langTag = $this->langTag();
        $authUserId = $this->user()?->id;

        $blockHashtagIds = UserBlock::type(UserBlock::TYPE_HASHTAG)->where('user_id', $authUserId)->pluck('block_id')->toArray();

        $hashtagQuery = Hashtag::whereNotIn('id', $blockHashtagIds)->isEnable();

        $hashtagQuery->when($dtoRequest->createDateGt, function ($query, $value) {
            $query->whereDate('created_at', '>=', $value);
        });

        $hashtagQuery->when($dtoRequest->createDateLt, function ($query, $value) {
            $query->whereDate('created_at', '<=', $value);
        });

        $hashtagQuery->when($dtoRequest->likeCountGt, function ($query, $value) {
            $query->where('like_count', '>=', $value);
        });

        $hashtagQuery->when($dtoRequest->likeCountLt, function ($query, $value) {
            $query->where('like_count', '<=', $value);
        });

        $hashtagQuery->when($dtoRequest->dislikeCountGt, function ($query, $value) {
            $query->where('dislike_count', '>=', $value);
        });

        $hashtagQuery->when($dtoRequest->dislikeCountLt, function ($query, $value) {
            $query->where('dislike_count', '<=', $value);
        });

        $hashtagQuery->when($dtoRequest->followCountGt, function ($query, $value) {
            $query->where('follow_count', '>=', $value);
        });

        $hashtagQuery->when($dtoRequest->followCountLt, function ($query, $value) {
            $query->where('follow_count', '<=', $value);
        });

        $hashtagQuery->when($dtoRequest->blockCountGt, function ($query, $value) {
            $query->where('block_count', '>=', $value);
        });

        $hashtagQuery->when($dtoRequest->blockCountLt, function ($query, $value) {
            $query->where('block_count', '<=', $value);
        });

        $hashtagQuery->when($dtoRequest->postCountGt, function ($query, $value) {
            $query->where('post_count', '>=', $value);
        });

        $hashtagQuery->when($dtoRequest->postCountLt, function ($query, $value) {
            $query->where('post_count', '<=', $value);
        });

        $hashtagQuery->when($dtoRequest->postDigestCountGt, function ($query, $value) {
            $query->where('post_digest_count', '>=', $value);
        });

        $hashtagQuery->when($dtoRequest->postDigestCountLt, function ($query, $value) {
            $query->where('post_digest_count', '<=', $value);
        });

        $orderType = match ($dtoRequest->orderType) {
            default => 'created_at',
            'createDate' => 'created_at',
            'like' => 'like_count',
            'dislike' => 'dislike_count',
            'follow' => 'follow_count',
            'block' => 'block_count',
            'post' => 'post_count',
            'postDigest' => 'post_digest_count',
        };

        $orderDirection = match ($dtoRequest->orderDirection) {
            default => 'desc',
            'asc' => 'asc',
            'desc' => 'desc',
        };

        $hashtagQuery->orderBy($orderType, $orderDirection);

        $hashtagData = $hashtagQuery->paginate($request->get('pageSize', 30));

        $hashtagList = [];
        $service = new HashtagService();
        foreach ($hashtagData as $hashtag) {
            $hashtagList[] = $service->hashtagList($hashtag, $langTag, $authUserId);
        }

        return $this->fresnsPaginate($hashtagList, $hashtagData->total(), $hashtagData->perPage());
    }

    // detail
    public function detail(string $hid)
    {
        $hashtag = Hashtag::where('slug', $hid)->first();

        if (empty($hashtag)) {
            throw new ApiException(37200);
        }

        if ($hashtag->isEnable(false)) {
            throw new ApiException(37201);
        }

        $langTag = $this->langTag();
        $authUserId = $this->user()?->id;

        $seoData = Seo::where('linked_type', Seo::TYPE_HASHTAG)->where('linked_id', $hashtag->id)->where('lang_tag', $langTag)->first();

        $item['title'] = $seoData->title ?? null;
        $item['keywords'] = $seoData->keywords ?? null;
        $item['description'] = $seoData->description ?? null;
        $data['items'] = $item;

        $service = new HashtagService();
        $data['detail'] = $service->hashtagDetail($hashtag, $langTag, $authUserId);

        return $this->success($data);
    }

    // interactive
    public function interactive(string $hid, string $type, Request $request)
    {
        $hashtag = Hashtag::where('slug', $hid)->isEnable()->first();
        if (empty($hashtag)) {
            throw new ApiException(37200);
        }

        $requestData = $request->all();
        $requestData['type'] = $type;
        $dtoRequest = new InteractiveDTO($requestData);

        InteractiveService::checkInteractiveSetting($dtoRequest->type, 'hashtag');

        $orderDirection = $dtoRequest->orderDirection ?: 'desc';

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $service = new InteractiveService();
        $data = $service->getUsersWhoMarkIt($dtoRequest->type, InteractiveService::TYPE_HASHTAG, $hashtag->id, $orderDirection, $langTag, $timezone, $authUserId);

        return $this->fresnsPaginate($data['paginateData'], $data['interactiveData']->total(), $data['interactiveData']->perPage());
    }
}
