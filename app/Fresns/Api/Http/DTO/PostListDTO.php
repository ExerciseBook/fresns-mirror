<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class PostListDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'uid' => ['nullable', 'integer'],
            'gid' => ['nullable', 'string'],
            'hid' => ['nullable', 'string'],
            'contentType' => ['nullable', 'string'],
            'digestState' => ['nullable', 'in:1,2,3'],
            'stickyState' => ['nullable', 'in:1,2,3'],
            'likeCountGt' => ['nullable', 'integer', 'lt:likeCountLt'],
            'likeCountLt' => ['nullable', 'integer', 'gt:likeCountGt'],
            'followCountGt' => ['nullable', 'integer', 'lt:followCountLt'],
            'followCountLt' => ['nullable', 'integer', 'gt:followCountGt'],
            'blockCountGt' => ['nullable', 'integer', 'lt:blockCountLt'],
            'blockCountLt' => ['nullable', 'integer', 'gt:blockCountGt'],
            'commentCountGt' => ['nullable', 'integer', 'lt:commentCountLt'],
            'commentCountLt' => ['nullable', 'integer', 'gt:commentCountGt'],
            'createTimeGt' => ['nullable', 'date_format:Y-m-d', 'before:createTimeLt'],
            'createTimeLt' => ['nullable', 'date_format:Y-m-d', 'after:createTimeGt'],
            'ratingType' => ['nullable', 'string'], // like,follow,block,comment,createTime
            'ratingOrder' => ['nullable', 'string'], // ASC,DESC
            'pluginRatingId' => ['nullable', 'integer'],
            'pageSize' => ['nullable', 'integer', 'between:1,15'],
            'page' => ['nullable', 'integer'],
            'mapId' => ['nullable', 'in:1,2,3,4,5,6,7,8,9,10'],
            'mapLng' => ['nullable', 'numeric'],
            'mapLat' => ['nullable', 'numeric'],
        ];
    }
}
