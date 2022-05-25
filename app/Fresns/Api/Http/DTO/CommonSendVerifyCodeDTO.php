<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Api\Http\DTO;

use Fresns\DTO\DTO;

class CommonSendVerifyCodeDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'accountType' => ['integer', 'required', 'in:1,2'],
            'account' => ['string', 'required'],
            'countryCode' => ['integer', 'nullable', 'required_if:type,2'],
            'useType' => ['integer', 'required', 'in:1,2,3,4,5'],
            'templateId' => ['integer', 'required', 'in:1,2,3,4,5,6,7'],
        ];
    }
}
