<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class CommonUploadLogDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'pluginUnikey' => ['string', 'nullable'],
            'objectType' => ['string', 'required'],
            'objectName' => ['string', 'required'],
            'objectAction' => ['string', 'required'],
            'objectResult' => ['integer', 'required', 'in:1,2,3'],
            'objectOrderId' => ['string', 'nullable'],
            'deviceToken' => ['string', 'nullable'],
            'moreJson' => ['string', 'nullable'],
        ];
    }
}
