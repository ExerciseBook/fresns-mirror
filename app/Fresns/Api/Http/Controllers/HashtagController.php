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
use App\Helpers\ConfigHelper;
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

        if ($dtoRequest->createDateGt) {
            $hashtagQuery->whereDate('created_at', '>=', $dtoRequest->createDateGt);
        }

        if ($dtoRequest->createDateLt) {
            $hashtagQuery->whereDate('created_at', '<=', $dtoRequest->createDateLt);
        }

        if ($dtoRequest->likeCountGt) {
            $hashtagQuery->where('like_count', '>=', $dtoRequest->likeCountGt);
        }

        if ($dtoRequest->likeCountLt) {
            $hashtagQuery->where('like_count', '<=', $dtoRequest->likeCountLt);
        }

        if ($dtoRequest->dislikeCountGt) {
            $hashtagQuery->where('dislike_count', '>=', $dtoRequest->dislikeCountGt);
        }

        if ($dtoRequest->dislikeCountLt) {
            $hashtagQuery->where('dislike_count', '<=', $dtoRequest->dislikeCountLt);
        }

        if ($dtoRequest->followCountGt) {
            $hashtagQuery->where('follow_count', '>=', $dtoRequest->followCountGt);
        }

        if ($dtoRequest->followCountLt) {
            $hashtagQuery->where('follow_count', '<=', $dtoRequest->followCountLt);
        }

        if ($dtoRequest->blockCountGt) {
            $hashtagQuery->where('block_count', '>=', $dtoRequest->blockCountGt);
        }

        if ($dtoRequest->blockCountLt) {
            $hashtagQuery->where('block_count', '<=', $dtoRequest->blockCountLt);
        }

        if ($dtoRequest->postCountGt) {
            $hashtagQuery->where('post_count', '>=', $dtoRequest->postCountGt);
        }

        if ($dtoRequest->postCountLt) {
            $hashtagQuery->where('post_count', '<=', $dtoRequest->postCountLt);
        }

        if ($dtoRequest->postDigestCountGt) {
            $hashtagQuery->where('post_digest_count', '>=', $dtoRequest->postDigestCountGt);
        }

        if ($dtoRequest->postDigestCountLt) {
            $hashtagQuery->where('post_digest_count', '<=', $dtoRequest->postDigestCountLt);
        }

        $orderType = match ($dtoRequest->orderType) {
            default => 'rating',
            'like' => 'like_me_count',
            'dislike' => 'dislike_me_count',
            'follow' => 'follow_me_count',
            'block' => 'block_me_count',
            'post' => 'post_count',
            'postDigest' => 'post_digest_count',
            'createDate' => 'created_at',
            'rating' => 'rating',
        };

        $orderDirection = match ($dtoRequest->ratingOorderDirectionrder) {
            default => 'asc',
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
        $hashtag = Hashtag::where('slug', $hid)->isEnable()->first();
        if (empty($hashtag)) {
            throw new ApiException(37200);
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

        InteractiveService::checkInteractiveSetting($dtoRequest->type, 'group');

        $orderDirection = $dtoRequest->orderDirection ?: 'desc';

        $langTag = $this->langTag();
        $timezone = $this->timezone();
        $authUserId = $this->user()?->id;

        $service = new InteractiveService();
        $data = $service->getUsersWhoMarkIt($dtoRequest->type, InteractiveService::TYPE_HASHTAG, $hashtag->id, $orderDirection, $langTag, $timezone, $authUserId);

        return $this->fresnsPaginate($data['paginateData'], $data['interactiveData']->total(), $data['interactiveData']->perPage());
    }
}
