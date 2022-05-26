<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class GroupListDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'type' => ['integer', 'required', 'in:1,2'],
            'parentGid' => ['string', 'nullable'],
            'recommend' => ['boolean', 'nullable'],
            'likeCountGt' => ['integer', 'nullable', 'lt:likeCountLt'],
            'likeCountLt' => ['integer', 'nullable', 'gt:likeCountGt'],
            'followCountGt' => ['integer', 'nullable', 'lt:followCountLt'],
            'followCountLt' => ['integer', 'nullable', 'gt:followCountGt'],
            'blockCountGt' => ['integer', 'nullable', 'lt:blockCountLt'],
            'blockCountLt' => ['integer', 'nullable', 'gt:blockCountGt'],
            'postCountGt' => ['integer', 'nullable', 'lt:postCountLt'],
            'postCountLt' => ['integer', 'nullable', 'gt:postCountGt'],
            'digestCountGt' => ['integer', 'nullable', 'lt:digestCountLt'],
            'digestCountLt' => ['integer', 'nullable', 'gt:digestCountGt'],
            'createTimeGt' => ['date_format:Y-m-d', 'nullable', 'before:createTimeLt'],
            'createTimeLt' => ['date_format:Y-m-d', 'nullable', 'after:createTimeGt'],
            'ratingType' => ['string', 'nullable', 'in:like,follow,block,post,digest,createTime,rating'],
            'ratingOrder' => ['string', 'nullable', 'in:asc,desc'],
            'pageSize' => ['integer', 'nullable', 'between:1,20'],
            'page' => ['integer', 'nullable'],
        ];
    }
}
