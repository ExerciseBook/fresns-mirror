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
            'mapId' => ['integer', 'nullable', 'in:1,2,3,4,5,6,7,8,9,10'],
            'mapLng' => ['numeric', 'nullable'],
            'mapLat' => ['numeric', 'nullable'],
            'uid' => ['integer', 'nullable'],
            'gid' => ['string', 'nullable'],
            'hid' => ['string', 'nullable'],
            'contentType' => ['string', 'nullable'],
            'digestState' => ['integer', 'nullable', 'in:1,2,3'],
            'stickyState' => ['integer', 'nullable', 'in:1,2,3'],
            'likeCountGt' => ['integer', 'nullable', 'lt:likeCountLt'],
            'likeCountLt' => ['integer', 'nullable', 'gt:likeCountGt'],
            'followCountGt' => ['integer', 'nullable', 'lt:followCountLt'],
            'followCountLt' => ['integer', 'nullable', 'gt:followCountGt'],
            'blockCountGt' => ['integer', 'nullable', 'lt:blockCountLt'],
            'blockCountLt' => ['integer', 'nullable', 'gt:blockCountGt'],
            'commentCountGt' => ['integer', 'nullable', 'lt:commentCountLt'],
            'commentCountLt' => ['integer', 'nullable', 'gt:commentCountGt'],
            'createTimeGt' => ['date_format:Y-m-d', 'nullable', 'before:createTimeLt'],
            'createTimeLt' => ['date_format:Y-m-d', 'nullable', 'after:createTimeGt'],
            'ratingType' => ['string', 'nullable', 'in:like,follow,block,comment,createTime'],
            'ratingOrder' => ['string', 'nullable', 'in:ASC,DESC'],
            'pluginRatingId' => ['integer', 'nullable'],
            'pageSize' => ['integer', 'nullable', 'between:1,20'],
            'page' => ['integer', 'nullable'],
        ];
    }
}
