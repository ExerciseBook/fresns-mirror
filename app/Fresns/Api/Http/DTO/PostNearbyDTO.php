<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class PostNearbyDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'unit' => ['string', 'nullable', 'in:km,mi'],
            'length' => ['integer', 'nullable'],
            'contentType' => ['string', 'nullable'],
            'mapId' => ['integer', 'nullable', 'in:1,2,3,4,5,6,7,8,9,10'],
            'mapLng' => ['numeric', 'nullable'],
            'mapLat' => ['numeric', 'nullable'],
            'pluginRatingId' => ['integer', 'nullable'],
            'pageSize' => ['integer', 'nullable', 'between:1,20'],
            'page' => ['integer', 'nullable'],
        ];
    }
}
