<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Words\Account\DTO;

use Fresns\DTO\DTO;

/**
 * Class VerifyAccountTokenDTO.
 */
class VerifyAccountTokenDTO extends DTO
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'platformId' => ['integer', 'required', 'between:1,13'],
            'aid' => ['string', 'required'],
            'aidToken' => ['string', 'required'],
        ];
    }
}
